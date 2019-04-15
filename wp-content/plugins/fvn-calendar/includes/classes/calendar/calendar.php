
<?php

/**
 * @package 	FVN-extension
* @author 		Vuong Anh Duong
* @link 		http://http://woafun.com/
* @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
**/


defined('ABSPATH') or die('Restricted access');

//designed by realmag777, http://PluginUs.net
class PN_Calendar {
	public $first_day_of_week = 0; //0 - sunday, 1 - monday
	public $current_year = null;
	public $current_month = null;
	public $current_day = null;
	public $show_year_selector = true;
	public $min_select_year = 2013;
	public $max_select_year = 2016;
	public $show_month_selector = true;
	public $route_id;

	public function __construct($atts = array()) {
		$this->min_select_year = HBFactory::getDate('-1 years')->format('Y');
		$this->max_select_year = HBFactory::getDate('+1 years')->format('Y');
		if (isset($atts['first_day_of_week'])) {
			$this->first_day_of_week = $atts['first_day_of_week'];
		}

		if (!isset($atts['year'])) {
			$this->current_year = date('Y');
		} else {
			$this->current_year = $atts['year'];
		}

		if (!isset($atts['month'])) {
			$this->current_month = date('m');
		} else {
			$this->current_month = $atts['month'];
		}

		if (!isset($atts['day'])) {
			$this->current_day = date('d');
		} else {
			$this->current_day = $atts['day'];
		}
		//***
		if (isset($atts['show_year_selector'])) {
			$this->show_year_selector = $atts['show_year_selector'];
		}

		if (isset($atts['show_month_selector'])) {
			$this->show_month_selector = $atts['show_month_selector'];
		}

		if (isset($atts['min_select_year'])) {
			$this->min_select_year = $atts['min_select_year'];
		}

		if (isset($atts['max_select_year'])) {
			$this->max_select_year = $atts['max_select_year'];
		}
		
		if (isset($atts['route_id'])) {
			$this->route_id = $atts['route_id'];
		}
	}

	/*
	 * Month calendar drawing
	*/

	public function draw($data = array(), $y = 0, $m = 0) {
		//***
		
		if ($m == 0 AND $m == 0) {
			$y = $this->current_year;
			$m = $this->current_month;
		}
		//***
		
		$data['week_days_names'] = $this->get_week_days_names(true);
		
		$data['cells'] = $this->generate_calendar_cells($y, $m);
		
		$data['month_name'] = $this->get_month_name($m);
		$data['year'] = $y;
		$data['month'] = $m;
		$data['events'] = array();//here you can transmit events from database (look PN_CalendarCell::draw($events))
		
		return $this->draw_html('calendar', $data);
	}

	private function generate_calendar_cells($y, $m) {
		$y = intval($y);
		$m = intval($m);
		//***
		$first_week_day_in_month = date('w', mktime(0, 0, 0, $m, 1, $y)); //from 0 (sunday) to 6 (saturday)
		$days_count = $this->get_days_count_in_month($y, $m);
		$cells = array();
		//***
		if ($this->first_day_of_week == $first_week_day_in_month) {
			for ($d = 1; $d <= $days_count; $d++) {
				$cells[] = new PN_CalendarCell($y, $m, $d);
			}
			//***
			$cal_cells_left = 5 * 7 - $days_count;
			$next_month_data = $this->get_next_month($y, $m);
			for ($d = 1; $d <= $cal_cells_left; $d++) {
				$cells[] = new PN_CalendarCell($next_month_data['y'], $next_month_data['m'], $d, false);
			}
		} else {
			//***
			if ($this->first_day_of_week == 0) {
				$cal_cells_prev = 6 - (7 - $first_week_day_in_month); //checked, is right
			} else {
				if ($first_week_day_in_month == 1) {
					$cal_cells_prev = 0;
				} else {
					if ($first_week_day_in_month == 0) {
						$cal_cells_prev = 6 - 1;
					} else {
						$cal_cells_prev = 6 - (7 - $first_week_day_in_month) - 1;
					}
				}
			}
			//***
			$prev_month_data = $this->get_prev_month($y, $m);
			$prev_month_days_count = $this->get_days_count_in_month($prev_month_data['y'], $prev_month_data['m']);

			for ($d = $prev_month_days_count - $cal_cells_prev; $d <= $prev_month_days_count; $d++) {
				$cells[] = new PN_CalendarCell($prev_month_data['y'], $prev_month_data['m'], $d, false);
			}

			//***
			for ($d = 1; $d <= $days_count; $d++) {
				$cells[] = new PN_CalendarCell($y, $m, $d);
			}
			//***
			//35(7*5) or 42(7*6) cells
			$busy_cells = $cal_cells_prev + $days_count;
			$cal_cells_left = 0;
			if ($busy_cells < 35) {
				$cal_cells_left = 35 - $busy_cells - 1;
			} else {
				$cal_cells_left = 42 - $busy_cells - 1;
			}
			//***
			if ($cal_cells_left > 0) {
				$next_month_data = $this->get_next_month($y, $m);
				for ($d = 1; $d <= $cal_cells_left; $d++) {
					$cells[] = new PN_CalendarCell($next_month_data['y'], $next_month_data['m'], $d, false);
				}
			}
		}
		//***
		return $cells;
	}

	public function get_next_month($y, $m) {
		$y = intval($y);
		$m = intval($m);

		//***
		$m++;
		if ($m % 13 == 0 OR $m > 12) {
			$y++;
			$m = 1;
		}

		return array('y' => $y, 'm' => $m);
	}

	public function get_prev_month($y, $m) {
		$y = intval($y);
		$m = intval($m);

		//***
		$m--;
		if ($m <= 0) {
			$y--;
			$m = 12;
		}

		return array('y' => $y, 'm' => $m);
	}

	public function get_days_count_in_month($year, $month) {
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
	}

	public static function get_month_name($m) {
		$names = self::get_monthes_names();
		return $names[intval($m)];
	}

	public function get_week_day_name($num, $shortly = false) {
		$names = $this->get_week_days_names($shortly);
		return $names[intval($num)];
	}

	public function get_week_days_names($shortly = false) {
		if ($this->first_day_of_week == 1) {
			if ($shortly) {
				return array(
						1 => 'Mo',
						2 => 'Tu',
						3 => 'We',
						4 => 'Th',
						5 => 'Fr',
						6 => 'Sa',
						7 => 'Su'
				);
			}

			return array(
					1 => 'Monday',
					2 => 'Tuesday',
					3 => 'Wednesday',
					4 => 'Thursday',
					5 => 'Friday',
					6 => 'Saturday',
					7 => 'Sunday'
			);
		} else {
			if ($shortly) {
				return array(
						0 => 'Su',
						1 => 'Mo',
						2 => 'Tu',
						3 => 'We',
						4 => 'Th',
						5 => 'Fr',
						6 => 'Sa'
				);
			}

			return array(
					0 => 'Sunday',
					1 => 'Monday',
					2 => 'Tuesday',
					3 => 'Wednesday',
					4 => 'Thursday',
					5 => 'Friday',
					6 => 'Saturday'
			);
		}
	}

	public static function get_monthes_names() {
		return array(
				1 => 'January',
				2 => 'February',
				3 => 'March',
				4 => 'April',
				5 => 'May',
				6 => 'June',
				7 => 'July',
				8 => 'August',
				9 => 'September',
				10 => 'October',
				11 => 'November',
				12 => 'December'
		);
	}

	public function draw_html($view, $data = array()) {
	
		@extract($data);
		ob_start();
		include('views/' . $view . '.php' );
	
		return ob_get_clean();
	}

}

//---------------------------------------------------------------------------------------
class PN_CalendarCell {

	public $cell_year = null;
	public $cell_month = null;
	public $cell_day = null;
	public $in_current_month = true;
	public $cell_date = null;

	public function __construct($y, $m, $d, $in_current_month = true) {
		$input = HBFactory::getInput();
		$this->cell_year = $y;
		$this->cell_month = sprintf('%02d',$m);
		$this->cell_day = sprintf('%02d',$d);
		$this->cell_date = "$this->cell_year-$this->cell_month-$this->cell_day";
		$this->in_current_month = $in_current_month;
		global $wpdb;
		$this->route_id = $input->get('route_id');
		
		if ($this->route_id) {
			$result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}HB_routerate WHERE route_id=$this->route_id AND date='$this->cell_date' LIMIT 0,1",OBJECT);
			$this->rate = reset($result);
		}else{
			$this->rate = 0;
		}
		
	}

	public function get_week_day_num() {
		return date('w', mktime(0, 0, 0, $this->cell_month, $this->cell_day, $this->cell_year)); //from 0 (sunday) to 6 (saturday);
	}

	public function draw($events) {
		$this_day_events = 0;
		if (is_array($events)) {
			if (isset($events[$this->cell_year][$this->cell_month][$this->cell_day])) {
				$this_day_events = count($events[$this->cell_year][$this->cell_month][$this->cell_day]);
			}
		} else {
			$events = array();
		}
		?>

<span class="pn_cal_cell_ev_counter"
<?php if ($this_day_events <= 0): ?> style="display: none;"
<?php endif; ?>><?php echo $this_day_events ?> </span>
<div data-year="<?php echo $this->cell_year ?>"
	data-month="<?php echo $this->cell_month ?>"
	data-day="<?php echo $this->cell_day ?>"
	data-week-day-num="<?php echo $this->get_week_day_num() ?>"
	class="<?php if ($this->in_current_month): ?>pn_this_month<?php else: ?>other_month<?php endif; ?> <?php echo $this->rate ? "hasrate":""; ?>">
	<?php
					
	echo $this->cell_day ?>
	
	<?php if ($this->rate){ 
		$icon = $this->rate->state ? 'icon-publish' : 'icon-unpublish';
		?>
	<a class="btn btn-small mgpopup" target="_blank" href="<?php echo wp_nonce_url(admin_url('admin.php?action=HB_execute_action&layout=rate_detail&date='.$this->cell_date.'&route_id='.$this->route_id.'&hb_admin_action=rate&task=show_rate_detail&'),'hb_action','hb_meta_nonce')?>">
		<span class="dashicons dashicons-admin-customizer"></span>
	</a>
	<input class="checkboxCell" type="checkbox"  value="<?php echo $this->cell_date; ?>" name="dates[]">
	<label style="font-size:80%"><i class="<?php echo $icon?>"></i>Adult:<?php echo $this->rate->adult?>, Child:<?php echo $this->rate->child?>, Infant:<?php echo $this->rate->infant?></label>
	<?php } else {?>
	
	<center>
		<a target="_blank" class="mgpopup"
			href="<?php echo wp_nonce_url(admin_url('admin.php?action=HB_execute_action&layout=rate_detail&date='.$this->cell_date.'&route_id='.$this->route_id.'&hb_admin_action=rate&task=show_rate_detail&'),'hb_action','hb_meta_nonce')?>">
			<span class="dashicons dashicons-plus"></span>
		</a>
	</center>
	
	<?php } ?>
	
</div>


<?php
	}

}


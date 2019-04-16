<?php 

class FvnCalendarHelper{

    static function getBooked($date=null){
        static $booked;
        if(!isset($booked)){
            FvnImporter::model('orders');
            $orders = (new FvnModelOrders())->getBooked();
            foreach($orders as $o){
                if(!isset($booked[$o->start])){
                    $booked[$o->start] = [];
                }
                $booked[$o->start][] = ['start_time'=>$o->start_time, 'end_time'=>$o->end_time];
            }
        }
        if($date){
            return $booked[$date];
        }
        return $booked;
    }

    static function getAvailable($date){
        if(!$date){
            return false;
        }
        $booked = self::getBooked($date);
        $result = [];
        $start_time = "08:00";
        $end_time = "24:00";
        foreach($booked as $period){
            $result[] = array(
                'start_time' => $start_time,
                'end_time' => $period['start_time']
            );
            $start_time = $period['end_time'];
        }
        $result[] = array(
            'start_time' => $start_time,
            'end_time' => $end_time
        );
        
        return $result;
    }
}
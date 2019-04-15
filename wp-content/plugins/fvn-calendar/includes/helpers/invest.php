<?php 

class FvnInvestHelper{

    static function getIvestPackage($id){
        static $packages;
        if(!isset($packages[$id])){
            HBImporter::model('investpackage','investpackagedaily');
            $model = new FvnModelInvestPackage();
            $packages[$id] = $model->getItem($id);
            if($packages[$id]->type == FvnParamInvestType::DAY['value']){
                $packages[$id]->rate = (new FvnModelInvestPackageDaily())->getRateByPackage($id);
            }
        }
        return $packages[$id];
    }
    static function caculateDrawAble($order,$check_end=false){
        $order = (array)$order;
        // debug($order);
        $result = [
            'total' => $order['total'],
            'revenue' => 0,
            'rate' => []
        ];
        // debug($result);
        $current = new DateTime();
        if($order['end']){
            $end = new DateTime($order['end']);
            if($current>$end){
                $current = $end;
            }
        }
        if($check_end){
            $current = new DateTime($order['end']);             
        }
        $start = new DateTime($order['start']);
        $diff = $current->diff($start)->format('%a');
        $current = $current->format('Y-m-d');
        $package = FvnInvestHelper::getIvestPackage($order['invest_package_id']);
        if(!$package->id){
            return $result;
        }
        if($package->type ==FvnParamInvestType::MONTHLY['value']){
            foreach($package->rate as $month => $rate){
                $date_expired = clone $start;
                $expired = $date_expired->modify('+'.($month*30).' days')->format('Y-m-d');
                if($current >= $expired){
                    $result['revenue'] = $rate/100*$order['total'];
                    $result['total'] = $order['total'] + $result['revenue'];
                    $result['rate'] = ['date'=>$month,'rate'=>$rate];
                }                
            }
        }else if($package->type==FvnParamInvestType::YEAR['value']){
            $expired = $start->modify('+1 year')->format('Y-m-d');
            if($current >= $expired){
                $result['revenue'] = (reset($package->rate))/100*$order['total'];
                $result['total'] = $order['total'] + $result['revenue'];
                $result['rate'] = ['date'=>$expired,'rate'=>reset($package->rate)];
            }  
        }else{
            $dates = [];
            $start_date = $start->format('Y-m-d');
            $early_date = [];
            foreach($package->rate as $k=>$rate){
                if($rate->date > $start && $rate->date < $current){
                    $dates[] = $rate;
                }else{
                    $early_date[] = $rate;
                }
            }
            //truong hop dung ngay bat dau goi lai ko co rate
            if(reset($dates)->date != (clone $start)->modify('+1 days')->format('Y-m-d')){
                array_unshift($dates,(object)array(
                    'rate'=>end($early_date)->rate,
                    'date'=>$start->modify('+1 days')->format('Y-m-d')
                ));
            }
            if(count($dates) < 1){                
                return $result;
            }   
            
            
            $result['revenue'] = 0;
            //lay ngay truoc do cua nhung ngay khong co trong rate log
            $current_date = reset($dates)->date;
            $current_rate = reset($dates)->rate;
            $j = 0;
            for($i = 1;$i < $diff; $i++){
                $caculate_date = $start->modify('+'.$i.' days')->format('Y-m-d');
                if($caculate_date==$dates[$j]->date){
                    $current_date = $dates[$j]->date;
                    $current_rate = $dates[$j]->rate;
                }
                $result['rate'][] = ['date'=>$caculate_date,'rate'=>$current_rate];
                $result['revenue'] += $current_rate/100*$order['total'];
            }
            $result['total'] += $result['revenue'];
        }
        
        return $result;
    }
}
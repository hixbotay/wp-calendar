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
        // foreach($)
        return $result;
    }
}
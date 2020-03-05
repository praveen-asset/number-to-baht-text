<?php

/**
 * Convert number amount to Thai text
 * 
 * author: Praveen Shekhawat
 * email:  shlokjodha@gmail.com
 * since:  2020-03-03
 * url: https://praveenshekhawat.com
 */

    // 0 ศูนย์
    // 1 หนึ่ง
    // 2 สอง
    // 9 เก้า
    // 10 สิบ =\= หนึ่งสิบศูนย์ ==> เลข 1 ที่หลักสิบ ให้เป็น ''
    // 11 สิบเอ็ด =\= หนึ่งสิบเอ็ด
    // 12 สิบสอง
    // 20 ยี่สิบ =\= สองสิบ ==> เลข 2 ที่หลักสิบ ให้เป็น 'ยี่'
    // 21 ยี่สิบเอ็ด
    // 22 ยี่สิบสอง
    // 30 สามสิบ ==> เลข 0 ที่ทุกหลัก ให้เป็น ''
    // 31 สามสิบเอ็ด
    // 32 สามสิบสอง
    // 100 หนึ่งร้อย
    // 101 หนึ่งร้อยหนึ่ง =\= หนึ่งร้อยเอ็ด
    // 200 สองร้อย
    // 1000 หนึ่งพัน

    // '1230' --> หนึ่งพันสองร้อยสามสิบ
// '1000000' --> หนึ่งล้าน

class Bahttext
{
    protected static $numbers = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];

    protected static $digits = ['สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];

    protected static $one_at_oneth = 'เอ็ด';

    protected static $two_at_tenth = 'ยี่';


    public static function convert($amount)
    {
    	$temp = explode(".", $amount);
    	$append = "";
    	if($temp[1]=='0' || $temp[1]=='00'){
    		$amount = $temp[0];
    		$append = 'ถ้วน';
    	}

        if ((int) $amount == 0) return 'ศูนย์บาท';

        $amount = (string) $amount;

        // find stang portion
        if (($dot = strpos($amount, '.')) > 0)
        {
            $stang = substr($amount, $dot+1);
            $amount = substr($amount, 0, $dot);
        }
        else{
            $stang = '';
        }

        // pad string to multiple of 6
        $amount = str_pad($amount, ceil(strlen($amount) / 6) * 6, ' ', STR_PAD_LEFT);

        $chunks = str_split($amount, 6);
        
        $text = '';

        while ( ! empty($chunks))
        {
            $segment = array_pop($chunks);
            $text = static::convertSegment($segment) . $text;
            if ( ! empty($chunks))
            {
                $text = 'ล้าน'.$text;
            }
        }

        $result = $text . 'บาท' . (empty($stang) ? '' : (static::convertSegment($stang) . 'สตางค์'));

        return $result .$append;
    }

    protected static function convertSegment($segment)
    {
        $segment = trim($segment);
        $length = strlen($segment);
        $last_digit = $length -1;

        if ($length == 1) return static::$numbers[(int)$segment];

        $text = '';

        for ($nth = $last_digit; $nth >= 0; $nth--)
        {
            // any zero in any digit
            if ($segment[$nth] == '0') continue;

            // oneth digit
            if ($nth === $last_digit)
            {
                $digit = '';
                $number = ($segment[$nth] == '1' and $segment[$nth -1] != '0')
                    ? static::$one_at_oneth
                    : static::$numbers[(int)$segment[$nth]];
            }

            // tenth digit
            elseif ($nth === $last_digit-1)
            {
                $digit = static::$digits[$last_digit - $nth -1];

                if ($segment[$nth] === '1')
                {
                    $number = '';
                }
                elseif ($segment[$nth] === '2')
                {
                    $number = static::$two_at_tenth;
                }
                else
                {
                    $number = static::$numbers[(int)$segment[$nth]];
                }
            }

            // other digits
            else
            {
                $number  = static::$numbers[(int)$segment[$nth]];
                $digit = static::$digits[$last_digit - $nth -1];
            }

            $text = ($number . $digit) . $text;
        }

        return $text;
    }
}


echo Bahttext::convert($_GET['number']);
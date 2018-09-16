<?php
error_reporting(E_ALL);

// $string = "convert unit 500feet into meter";
// $string = "How many cubic feet are there in a room measuring 5m x 10m x 2m?";
// $string = "convert unit 5kilo meter equals to m";
// $string = "Convert milligrams into meter.";
// $string = "book a hotel for 6 month in LA but it should be charge only once in a month";
// $string = "What is the height in meters of a 5'3\" person?";
// $string = "Convert 3598 grams into pounds";
// $string = "1 light year = meters";
// $string = "HOW MUCH IN 70 DEGREE FARANIITE INTO CELCIUS";
// $string = "Convert 20 degrees Celcius to Degrees fehranite";
// $string = "HOW MUCH IS 35 PSI IN TO BAR";
// $string = "HOW MUCH IS 4KM IN TO METER";
// $string = "CONVERT 4KM IN TO METER";
// $string = "HOW MUCH IS ACRE IN TO SQ FOOT";
// $string = "Whats 4years into days.";
// $string = "Convert kardo cms to kms.";
// $string = "If I have 50litres of water, how much do I have in millilitres.";
// $string = "Convert 54 'C into F";
// $string = "If I have 50kgs of chicken meat, how much do I have it in milligrams";
// $string = "Convert meter into centimeter.";
// $string = "Convert inches into feet.";
// $string = "How much is my height in cms if my height is 6feet.";
// $string = "Convert  20 quintals of milk into litres.";
// $string = "Convert light year into kilometer.";
// $string = "convert meter per second into kilometers per hour.";
// $string = "convert 5 gallon to liter";
// $string = "convert Kg/m3 to g/cm3";
// $string = "50 fl oz to cm3";
// $string = "What is the mass of a 120 lb person in grams?";
// $string = "6 gallons of gasoline costs $21.00. How does a liter cost?";
// $string = "A beaker contains 578 mL of water. What is the volume in quarts?";
// $string = "What is 7.86 kL in dL?";
// $string = "If you are going 55 mph, what is your speed in m per second?";
$string = "A chemical costs $5.25 per pound. What would be the cost of 10.0 kg of the chemical?";
// $string = "A block occupies 0.2587 ft3. What is its volume in mm3?";
// $string = "Gallium is a metal that can melt at 302.93 K. What is the temperature in C?";
// $string = "Aluminum metal melts at 660.37 C. What is the temperature in Kelvin?";
// $string = "The average surface temperature on Mars is -63 C. What is the temperature in F?";
// $string = "A block occupies 0.2587 ft3. What is its volume in mm3?";
// $string = "What is the volume of a 12 oz can of soda in mL?";
// $string = "1 hectare is equal to square meter";
// $string = "1 erg/sec = j/s";
// $string = "1 dekameter = meters";
// $string = "convert units min to decade";
// $string = "1 dyne is equal to Newton?";


converter($string);

function converter($string){
    $unitsarr = definedunits();
    $unitmatcharr = regexunit($string);
    $response = array();

    $unit1 = isset($unitmatcharr[0][0]) ? $unitmatcharr[0][0] : '';
    $unitval1 = isset($unitmatcharr[0][1]) ? $unitmatcharr[0][1] : 1;
    $unit2 = isset($unitmatcharr[1][0]) ? $unitmatcharr[1][0] : '';
    $unitval2 = isset($unitmatcharr[1][1]) ? $unitmatcharr[1][1] : 1;

    if($unit1 && $unit2 && ($unitval1 == 1 || $unitval2 == 1)){
        if($unitval2 != 1){
            $unit1t = $unit1;
            $unitval1t = $unitval1;
            
            $unit1 = $unit2;
            $unitval1 = $unitval2;
            $unit2 = $unit1t;
            $unitval2 = $unitval1t;
        }

        $info = isset($unitsarr[$unit1][$unit2]) ? $unitsarr[$unit1][$unit2] : array();
        if ($info) {
            $a = $info['a']??'0';
            $b = $info['b']??'0';
            $n = $info['n']??'1';
            $value = $unitval1;
            $y = ($value)*($a**$n)+$b;

            if ($y > 0 && $y < 1) {
                $output = $y;
            }else{
                $output = round($y,4);
            }

            $response = array(
                'from'  => $unit1,
                'to'    => $unit2,
                'value' => $value,
                'output'=> $output,
            );
        }else{

            $value = $unitval1;
            $response = array(
                'from'  => $unit1,
                'to'    => $unit2,
                'value' => $value,
                'output'=> '-1'
            );
        }
        
    }
    else{
        //proportion logic here
    }
 
    print_r($response);
    return $response;
}

function regexunit($string){  
    $string2 = $string;
    $string = preg_replace('/(\d+\.?\d*)/', ' $1 ', strtolower($string));
    $string = str_replace(array('-', ','), array(' - ', ' , '), $string);
    $string = preg_replace('!\s+!', ' ', strtolower($string));

    $simplewordstonumberarr = getsimplewordstonumber($string);
    
    if($simplewordstonumberarr){
        foreach ($simplewordstonumberarr as $key => $value) {
            $string = preg_replace('/\b('.$key.')\b/i', $value, $string);
        }
    }
    $unitaliasarr = definedunitalias();
    $unitsarr = definedunits();
    
    foreach ($unitaliasarr as $key => $unitalias) {
        $string = preg_replace('/\b('.implode('|', $unitalias).')s?\b/i', $key, $string);
    }
    $string = stripslashes($string);
    $unitsarrkey = array_keys($unitsarr);
    $pattern = '\b('.implode('|', $unitsarrkey).')\b';
    $unitmatcharr = array();
    
    @preg_match_all('/'.str_replace('/', '\/', $pattern).'/i', $string, $matches);
    if ($matches[0]) {
        foreach ($matches[0] as $k => $value) {
            $_unit = $matches[1][$k];
            $_val = 1;
            if (preg_match('/(-?\s?\d*\.?\d*)\s(?:' . str_replace('/', '\/', $_unit) . ')/i', $string, $match)) {
               $_val = ($match[1]) ? $match[1] : 1;
               $_val = preg_replace('!\s+!', '', $_val);
            }
                $unitmatcharr[] = array($_unit, $_val);
        }
    }   
    return $unitmatcharr;
}

function getwordstonumbers($string) {
    $string = trim(preg_replace('!\s+!', ' ', strtolower($string)));
    $string3 = $string;
    if(preg_match('/\b(hundredth|thousandth|millionth|billionth|trillionth|hundredths|thousandths|millionths|billionths|trillionths)\b/i', $string, $matches)){
        $stringarr = preg_split( "/\b(&|and)\b/i", $string );
    }
    else{
        $stringarr = array($string);
    }
    $finalnumarr = array();
    foreach ($stringarr as $string) {
        $string2 = $string;
        $data = strtolower($string);
        $str = $data;
        $wr = array(
            'zero',
            'a',
            'one',
            'two',
            'three',
            'four',
            'five' ,
            'six',
            'seven',
            'eight',
            'nine',
            'ten',
            'eleven',
            'twelve',
            'thirteen',
            'fourteen',
            'fifteen',
            'sixteen',
            'seventeen',
            'eighteen',
            'nineteen',
            'twenty',
            'thirty',
            'forty',
            'fourty', // common misspelling
            'fifty',
            'sixty',
            'seventy',
            'eighty' ,
            'ninety',
            'ninty',
            'hundred',
            'thousand',
            'million',
            'billion',
            'trillion',
            'lakh',
            'crore',
            'arab',
            'kharab',
            'hundreds',
            'thousands',
            'millions',
            'billions',
            'trillions',
            'lakhs',
            'crores',
            'arabs',
            'kharabs',
        );

        $str = str_replace(array('-', ','), ' ', $str);
        $strarr = explode(' ', $str);
        $fstrarr = array();
        foreach ($strarr as $word) {
            if($word && in_array(trim($word), $wr)){
                $fstrarr[] = $word;
            }
        }
        $str = implode(' ', $fstrarr);

        $data = $str;

        // Replace all number words with an equivalent numeric value
        $data = strtr(
                $data, array(
            'zero' => '0',
            'a' => '1',
            'one' => '1',
            'two' => '2',
            'three' => '3',
            'four' => '4',
            'five' => '5',
            'six' => '6',
            'seven' => '7',
            'eight' => '8',
            'nine' => '9',
            'ten' => '10',
            'eleven' => '11',
            'twelve' => '12',
            'thirteen' => '13',
            'fourteen' => '14', 
            'fifteen' => '15',
            'sixteen' => '16',
            'seventeen' => '17',
            'eighteen' => '18',
            'nineteen' => '19',
            'twenty' => '20',
            'thirty' => '30',
            'forty' => '40',
            'fourty' => '40', // common misspelling
            'fifty' => '50',
            'sixty' => '60',
            'seventy' => '70',
            'eighty' => '80',
            'ninety' => '90',
            'ninty' => '90',
            'hundred' => '100',
            'thousand' => '1000',
            'million' => '1000000',
            'billion' => '1000000000',
            'trillion' => '1000000000000',
            'lakh' => '100000',
            'crore' => '10000000',
            'arab' => '1000000000',
            'kharab' => '100000000000',
            'hundreds' => '100',
            'thousands' => '1000',
            'millions' => '1000000',
            'billions' => '1000000000',
            'trillions' => '1000000000000',
            'lakhs' => '100000',
            'crores' => '10000000',
            'arabs' => '1000000000',
            'kharabs' => '100000000000',
            'and' => '',
                )
        );

        $_arr = preg_split('/[\s-]+/', $data);
        $arr2 = array();
        foreach ($_arr as $k => $v){
            if($k == 0){
               $arr2[] = $v; 
            }else{
                if(strlen($v) <= 2 && strlen($_arr[$k-1]) <= 1){
                    $arr2[count($arr2)-1] = $arr2[count($arr2)-1].$v;
                }
                elseif(strlen($v) <= 1 && strlen($_arr[$k-1]) <= 2){
                    $arr2[count($arr2)-1] = $arr2[count($arr2)-1]+$v;
                }
                else{
                    $arr2[] = $v;
                }
            }
        }

        $data = implode(' ', $arr2);

        // Coerce all tokens to numbers
        $parts = array_map(
                function ($val) {
            return floatval($val);
        }, preg_split('/[\s-]+/', $data)
        );

        $stack = new SplStack; // Current work stack
        $sum = 0; // Running total
        $last = null;

        foreach ($parts as $part) {
            if (!$stack->isEmpty()) {
                // We're part way through a phrase
                if ($stack->top() > $part) {
                    // Decreasing step, e.g. from hundreds to ones
                    if ($last >= 1000) {
                        // If we drop from more than 1000 then we've finished the phrase
                        $sum += $stack->pop();
                        // This is the first element of a new phrase
                        $stack->push($part);
                    } else {
                        $stack->push($stack->pop() + $part);
                    }
                } else {
                    // Increasing step, e.g ones to hundreds
                    $stack->push($stack->pop() * $part);
                }
            } else {
                // This is the first element of a new phrase
                $stack->push($part);
            }

            // Store the last processed part
            $last = $part;
        }
        $num = $sum + $stack->pop();

        if(preg_match('/\b(hundredth|thousandth|millionth|billionth|trillionth|hundredths|thousandths|millionths|billionths|trillionths)\b/i', $string2, $matches)){
            if($matches[0] == 'hundredth' || $matches[0] == 'hundredths'){
                $num = $num*0.01;
            }elseif($matches[0] == 'thousandth' || $matches[0] == 'thousandths'){
                $num = $num*0.001;
            }elseif($matches[0] == 'millionth' || $matches[0] == 'millionths'){
                $num = $num*0.000001;
            }elseif($matches[0] == 'billionth' || $matches[0] == 'billionths'){
                $num = $num*0.000000001;
            }elseif($matches[0] == 'trillionth' || $matches[0] == 'trillionths'){
                $num= $num*0.000000000001;
            }
        }
        $finalnumarr[] = $num;
    }
    
    $num = 0;
    foreach ($finalnumarr as $key => $value) {
        if($key == 0){
            $num = $num+$value;
        }else{
            $num = $num.''.  ltrim(number_format(rtrim(sprintf('%.20f', $value), '0'), 9), '0');
        }
    }
    
    return array($num);
}

function getsimplewordstonumber($string){
    $string2 = $string;
    $string = strtolower(preg_replace('!\s+!', ' ', $string));
    $simplewordstonumberarr = array();
    
    $wr = array(
        'zero',   
        'a',
        'one',
        'two',
        'three',
        'four',
        'five' ,
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
        'eleven',
        'twelve',
        'thirteen',
        'fourteen',
        'fifteen',
        'sixteen',
        'seventeen',
        'eighteen',
        'nineteen',
        'twenty',
        'thirty',
        'forty',
        'fourty', // common misspelling
        'fifty',
        'sixty',
        'seventy',
        'eighty' ,
        'ninety',
        'ninty',
        'hundred',
        'thousand',
        'million',
        'billion',
        'trillion',
        'lakh',
        'crore',
        'arab',
        'kharab',
        'hundreds',
        'thousands',
        'millions',
        'billions',
        'trillions',
        'lakhs',
        'crores',
        'arabs',
        'kharabs',
    );
    
    $strarr = explode(' ', $string);
    $fstrarr = array();
    $i = 0;
    foreach ($strarr as $word) {
        if($word && in_array(trim($word), $wr) && ((!isset($fstrarr[$i][0]) && $word != 'a') || isset($fstrarr[$i][0]))){
            $fstrarr[$i][] = $word;
        }
        elseif(isset($fstrarr[$i][0])){
            $i++;
        }
    }
    
    if($fstrarr){
        foreach ($fstrarr as $key => $value) {
            $str = implode(' ', $value);
            $wordstonumbersarr = getwordstonumbers($str);
            $simplewordstonumberarr[$str] = isset($wordstonumbersarr[0]) ? $wordstonumbersarr[0] : '';
        }
    }
    
    if($simplewordstonumberarr){
        $arrKeysLength = array_map('strlen', array_keys($simplewordstonumberarr));
        array_multisort($arrKeysLength, SORT_DESC, $simplewordstonumberarr);
    }
    
    return $simplewordstonumberarr;
}

function definedunitalias() {
    $unitalias = array(

        //Energy Units
        "joule\/s" => array('j\/sec', 'joules\/sec', 'joule\/sec', 'joules\/second', 'joule\/second','joul\/sec','J per second','J\/s','J\/sec','joule','joul','joules'),
        "erg\/s" => array('erg\/sec', 'ergs\/second', 'erg\/second', 'ergs\/second','erg per second','erg \/ sec','erg \/ s','erg','ergs'),

        //  Area units alias
        'm2'    => array('square metre', 'sq m', 'squaremetre', 'meter square', 'metersquare', 'metre square', 'square meter','m2','m 2', 'm\^2', 'sqm','sq. metre','sq. meter'),
        'ha'    => array('hectare', 'ha', 'hect'),
        'km2'   => array('square-kilometre', 'sq km', 'km 2','kilometre square', 'square-kilometres', 'km2', 'km\^2', 'squarekilometer', 'kilometersquare','sq. kilometre','sq. kilometre','sq. killometre','sq. killometre'),
        'in2'   => array('square inch', 'sq in', 'inch sqaure', 'in2','in 2', 'in\^2', 'squareinches', 'square inches', 'squareinch', 'inchsquare','sq. inch','sq. inches'),
        'ft2'   => array('square-feet', 'square-feets', 'sq ft', 'ft\^2', 'ft2', 'ft 2', 'sqft', 'squarefeets', 'squarefeet','square-foot', 'square-foots','squarefoots', 'squarefoot','sq. foot'),
        'yd2'   => array('square yard', 'square yards', 'sq yd', 'yd\^2', 'yd2','yd 2', 'squareyards', 'squareyard', 'sqyd'),
        'acre'  => array('acre', 'ac', 'acres','acr'),
        'mi2'   => array('square mile', 'sq mi', 'square miles', 'squaremile', 'squaremiles', 'sqmi', 'mi2', 'mi 2', 'mi\^2','sq. mile','sq. miles'),

        //Volume Units

        'm3'     => array('cubic meter','m³','m\^3','m3','m 3','cubic-meter','metrecube','metre-cube','cubic-metre','metrecube','metre-cube','cubic metre'),
        'dm3'    => array('dm³','dm\^3','dm3','dm 3','cubic decimeter','cubic decimetre','cubic-decimeter','cubic-decimetre','decimeter cube','decimetre cube','decimeter-cube','decimetre-cube'),
        'cm3'    => array('cubic centimeter','cm³','cm\^3','cm3','cm 3','centimeter cube','centimetre cube','cubic centimetre','centimeter-cube','centimetre-cube','cubic-centimetre','cubic-centimeter'),
        'in3'    => array('cubic inch','in³','in\^3','in3','in 3','inch3','inch cube','cubic inches'),
        'ft3'    => array('cubic foot','ft³','ft\^3','ft3','ft 3','foot cube','cubic-foot','foot-cube'),
        'yd3'    => array('cubic yard','yd³','yd\^3','yd3','yd 3','cubic-yard','yard-cube','yard cube','cubicyard','yardcube'),
        'liter'  => array('liter','l','liters','lit','litrs','litr','ltr','ltrs','litre'),
        'dl'     => array('deciliter','dl','deciliters','dL','deci-liters','deci-liter','deci-litres','deci-litre','decilitre','decilitres'),
        'cl'     => array('centiliter','cl','centiliters','cL','centi-liters','centi-liter','centi-litres','centi-litre','centilitre','centiliters'),
        'fl oz'  => array('fluid ounce','fl oz','fluid oz','fl ounce','fluidounce','fluid-ounce','oz'),
        'ga'     => array('gallon','ga','gal','gallons','galon','galons','us galon','U.S. galon','usgalon','us galon','U.S. galon','usgalon','u.s. galon','u.s. galon','us gallon','U.S. gallon','usgallon','us gallon','U.S. gallon','usgallon','u.s. gallon','u.s. gallon'),
        'barrel' => array('barrel','bbl','barrels','barel','barels'),
        'pint'   => array('pint','pt','pints'),
        'ml'     => array('milliliter','ml','milliliters','milli-liter','milli-liters','milli-litre','milli-litres','mili-liter','mili-liters','mili-litre','mili-litres','mililitre','mililitres','milli liter','milli liters','milli litre','milli litres','mili liter','mili liters','mili litre','mili litres','millilitre'),
        "quarts" => array('quarts','quart','quartz','qt','us quart','U.S. quart','usquart','us quarts','U.S. quarts','usquarts','u.s. quarts','u.s. quart'),
        "mm3"    => array('mm³','mm\^3','mm 3','mm3','cubic millimeter','cubic millimetre','cubic-millimeter','cubic-millimetre','millimeter cube','millimetre cube','millimeter-cube','millimetre-cube','cubic milimeter','cubic milimetre','cubic-milimeter','cubic-milimetre','milimeter cube','milimetre cube','milimeter-cube','milimetre-cube'),
        "kl"     => array('kiloliter','kl','kiloliters','kL','kilo-liters','kilo-liter','kilo-litres','kilo-litre','kilolitre','kilolitres','killoliter','killoliters','killo-liters','killo-liter','killo-litres','killo-litre','killolitre','killolitres'),


        // Lenght Alias
        "km"   => array('kilometer', 'kilo meter', 'kilometre', 'kilo metre', 'km', 'KM','kilomtr','kms','kilometers', 'kilo meters', 'kilometres', 'kilo metres', 'KMS','kilomtrs'),
        "cm"   => array('cm', 'centimeter', 'centimetre','centi meter','centi-meter','centi metre','centi-metre','centimtr','cms', 'centimeters', 'centimetres','centi meters','centi-meters','centi metres','centi-metres','centimtrs'),
        "dm"   => array('dm', 'decimeter', 'decimetre','deci meter','deci-meter','deci metre','deci-metre','decimtr','dms', 'decimeters', 'decimetres','deci meters','deci-meters','deci metres','deci-metres','decimtrs'),
        "mm"   => array('mm', 'milimeter', 'milimetre','mili meter','mili-meter','mili metre','mili-metre','millimtr','mms', 'milimeters', 'milimetres','mili meters','mili-meters','mili metres','mili-metres','milimtrs','millimeter', 'millimetre','milli meter','milli-meter','milli metre','milli-metre','millimtr','mms', 'millimeters', 'millimetres','milli meters','milli-meters','milli metres','milli-metres','millimtrs'),
        "microm"   => array('µm', 'microm','micrometer', 'micrometre','micro meter','micro-meter','micro metre','micro-metre','micromtr','µms', 'micrometers', 'micrometres','micro meters','micro-meters','micro metres','micro-metres','micromtrs'),
        "nm"   => array('nm', 'nanometer', 'nanometre','nano meter','nano-meter','nano metre','nano-metre','nanomtr','nms', 'nanometers', 'nanometres','nano meters','nano-meters','nano metres','nano-metres','nanomtrs'),
        "pm"   => array('pm', 'picometer', 'picometre','pico meter','pico-meter','pico metre','pico-metre','picomtr','pm', 'picometer', 'picometres','pico meters','pico-meters','pico metres','pico-metres','picomtrs'),
        "inch" => array('inch', 'inches','\"'),
        "ft"   => array('ft', 'foot', 'feet','\'','foots', 'feets'),
        "yd"   => array('yd', 'yard', 'yards'),
        "mi"   => array('mi', 'mile', 'miles'),
        "hand"    => array('hands', 'hand'),
        "ly"   => array('ly', 'lightyear','light year','light-year'),
        "au"   => array('au', 'astronomical', 'astronomical unit'),
        "pc"   => array('pc', 'parsec'),
        "m"    => array('meter', 'mtr', 'metre', 'm','meters','metres','mtrs'),
        "decam"    => array('dam', 'decameter', 'decametre','deca meter','deca-meter','deca metre','deca-metre','decamtr','dams', 'decameters', 'decametres','deca meters','deca-meters','deca metres','deca-metres','decamtrs','dekameter', 'dekametre','deka meter','deka-meter','deka metre','deka-metre','dekamtr', 'dekameters', 'dekametres','deka meters','deka-meters','deka metres','deka-metres','dekamtrs'),

       
       // Speed Units
        'km\/h' => array('km\/h', 'km per hour', 'kilometerperhour','kilometers per hour','kilometersperhour', 'kilometer per hour', 'kilometer\/hour', 'km\/hr', 'kilometer\/hr', 'kmph'),
        'm\/s' => array('meter\/second', 'm per second', 'meterpersecond', 'meter per second', 'meter\/s', 'm\/s', 'meter\/sec', 'mps'),
        'ft\/min' => array('feet\/minute', 'ft per minutes', 'feetperminute', 'feet per minute', 'feet\/min', 'ft\/min', 'feet\/minutes', 'fpm'),
        'ft\/s' => array('feet\/second', 'ft per second', 'feetpersecond', 'feet per second', 'feet\/sec', 'ft\/sec', 'feet\/second', 'fps'),
        'yards\/min' => array('yards\/second', 'yd per second', 'yardspersecond', 'yards per second', 'yards\/sec', 'yd\/sec', 'yards\/second', 'ydps'),
        'mph' => array('meter\/hour', 'm per hour', 'meterperhour', 'meter per hour', 'meter\/hr', 'm\/hr', 'meter\/hr', 'mph'),
        'knots' => array('knots', 'knts', 'knot'),

        // Temperature units
        'cel' => array('c', 'celcius', 'cel', 'degree celcius','degree c','degrees Celcius', 'degreecelcius', 'degree-celsius', 'celsius', 'degree celsius', 'degreecelsius','°C','celcius'),
        'kel' => array('k', 'kelvin', 'kel', 'degreekelvin','degree k','degree kelvin', 'degree-kelvin'),
        'feh' => array('f', 'fehranite', 'fahrenheit', 'feh', 'degree f','degree-fehranite','degrees fehranite','degrees fahrenheit','fehranite', 'degreefehranite', 'degree fehranite', 'degree-fahrenheit', 'degreefahrenheit', 'degree fahrenheit','°F','degree faraniite','degree-faraniite','degrees faraniite','degrees-faraniite'),
        'rank' => array('rank', 'r', 'rankine scale', 'rankine', 'degree rankine','degree r', 'degree-rankine', 'degreerankine', 'degree r','°Ra','Ra'),
        
        //  Angle Alias
        'deg'   => array('degree', 'deg', 'degre'),
        'rad'   => array('radian', 'rad', 'radians'),
        'sec'   => array('second', 'sec', 'seconds'),
        'min'   => array('minute', 'minutes', 'min'),
        'grad'  => array('gradian', 'grad', 'gradians'),   

        // Frequency units
        'ghz' => array('ghz', 'gigahertz', 'giga hertz','giga-hertz'),
        'mhz' => array('mhz', 'megahertz', 'mega hertz','mega-hertz'),
        'khz' => array('khz', 'kilohertz', 'kilo hertz','kilo hertz'),
        'hz' => array('hz', 'hertz'),

        // Volt Units
        "mv" => array('mv', 'milivolt', 'mili-volt', 'mili volt','milivolts', 'mili-volts', 'mili volts'), //milivolt
        "microv" => array('micro volt','microv', 'μv', 'microvolt', 'micro-volt','microvolts', 'micro-volts','micro volts'),
        "kv" => array('kv', 'kilovolt', 'kilo volt', 'kilo-volt','kilovolts', 'kilo volts', 'kilo-volts'),
        "v" => array('v', 'volt','volts'),

        // Electric Charge Units
        "mc" => array('mc', 'milicoulomb', 'mili-coulomb', 'mili coulomb','milicoulombs', 'mili-coulombs', 'mili coulombs'), //milicoulomb
        "microc" => array('micro coulomb','microc', 'μc', 'microcoulomb', 'micro-coulomb','micro coulombs','microcoulombs', 'micro-coulombs'),
        "nc" => array('nc', 'nanocoulomb', 'nano coulomb', 'nano-coulomb', 'nanocoulombs', 'nano coulombs', 'nano-coulombs'),
        "c" => array('c', 'charge'),

        // Pressure Units
        "bar"   => array('bar', 'bars'),
        "atm"   => array('atm', 'atmosphere', 'atmospheres','atmosphere pressure','atmosphere-pressure'),
        "torr"  => array('torr','tor','lbf\/in2'),
        "pa"    => array('pa', 'pascal', 'pascle', 'pascals'),
        "psi"   => array('psi', 'pound per square inch', 'pound \/ square inch', 'pound\/square inch','poundpersquareinch'),

         // Power Units
        "w" => array('w', 'watt', 'watts','wat'),
        "kw" => array('kw', 'kwatt', 'kwatts','kwat','kilow', 'kilowatt', 'kilowatts','kilowat','kilo-w', 'kilo-watt', 'kilo-watts','kilo-wat','killow', 'killowatt', 'killowatts','killowat','kilo w', 'kilo watt', 'kilo watts','kilo wat','killo w', 'killo watt', 'killo watts','killo wat','killo-w', 'killo-watt', 'killo-watts','killo-wat'),

        //digital information
        'bit' => array('bit','bits','b'),
        'byte' => array('byte','bytes'),
        'kb' => array('kilo byte','kb','kilobytes','kilo-bytes','kilo-byte','kbyte'),
        'mb' => array('mega byte','mb','megabytes','mega-bytes','mega-byte','mbyte'),
        'gb' => array('gega byte','gb','gegabytes','gega-bytes','gega-byte','gbyte'),
        'tb' => array('tera byte','tb','terabytes','tera-bytes','tera-byte','tbyte'),
        'pb' => array('peta byte','pb','petabytes','peta-bytes','peta-byte','pbyte'),

        //force
        'dyne'   => array('dyne','dyn','dine'),
        'newton' => array('newton','n'),

        //mass
        'kg' => array('kilogram','kg','kilograms','kgs','kilogm'),
        'grms ' => array('grams ','gm','gms','gram'),
        'mg' => array('milligrams','mg','milligram','milligm'),
        'cg' => array('centigrams','cg','centigram','centigm'),
        'dg' => array('decigrams','dg','decigm','decigram'),
        'hg' => array('hectogram','hg','hectograms','hectogm','hectogms','hectogrm'),
        'microg' => array('microgram','microgram','µg','micrograms','microgms'),
        'pounds' => array('pounds','pound','ponds','lbs','lb'),
        'ounce' => array('ounces','ounce'),
        'stone' => array('stone','stones'),
        'ton' => array('ton','tonne','tons','tonnes','tone','tones'),
        'quintal' => array('quintal','cwt','quintals'),

        // Density Units
        "kg\/m 3" => array('kilogrampermetercube', 'kilogram per meter cube', 'kilogrampercubicmeter', 'kilogrampercubicmeter', 'kilogram \/ cubic meter', 'kilogram\/cubic meter', 'kg\/m 3', 'kg\/m3', 'kg\/m\^ 3', 'kilo-gram per meter cube', 'kilo-gram per cubic meter'),
        "g\/cm 3" => array('grampermetercube', 'gram per meter cube', 'grampercubicmeter', 'grampercubicmeter', 'gram \/ cubic meter', 'gram\/cubic meter', 'g\/m 3', 'g\/m3', 'g\/m\^ 3', 'gram per meter cube', 'gram per cubic meter'),

        //Time
        'millisecond' => array('millisecond','ms','milli-second','milisecond','mili-second','milliseconds','milli-seconds','miliseconds','mili-seconds'),
        'microsecond' => array('microsecond','µs','micro-second','microsec','micro-sec','microseconds','micro-seconds','microsecs','micro-secs'),
        'nanosecond'  => array('nanosecond','nano-second','ns','nanoseconds','nano-seconds','nanosec','nano-sec','nano-secs','nanosecs', 'nano secs','nano sec','nano second','nano seconds'),
        'hour'        => array('hour','hr','hrs'),
        'minute'      => array('minute','min','minutes','mins'),
        'second'      => array('second','sec','seconds','secs'),
        'day'         => array('day','days'),
        'week'        => array('week','weeks','wk'),
        'month'       => array('month','months','mon','mo'),
        'year'        => array('years','year','yr','yrs'),
        'decade'      => array('decades','decade','decad'),
        'century'     => array('centurys','century'),

    );
    return $unitalias;
}

function definedunits() {
    $units = array(

        #------Energy Units---##

        "joule/s" => array(
            "erg/s" => array("a" => 1*10000000, "b" => 0, "n" => 1),
            "joule/s" => array("a" => 1, "b" => 0, "n" => 1),
        ),

        "erg/s" => array(
            "erg/s" => array("a" => 1, "b" => 0, "n" => 1),
            "joule/s" => array("a" => 1/10000000, "b" => 0, "n" => 1),
        ),

        #------END Energy Units---##
        
        // Speed Units
        "km/h"  => array(
            "km/h"  => array("a" => 1, "b" => 0, "n" => 1),
            "m/s"   => array("a" => 0.278, "b" => 0, "n" => 1),
            "ft/s"  => array("a" => 0.91, "b" => 0, "n" => 1),
            "mph"   => array("a" => 0.62, "b" => 0, "n" => 1),
            "knots" => array("a" => 0.54, "b" => 0, "n" => 1),
        ),
        "m/s" => array(
            "km/h"  => array("a" => 3.6, "b" => 0, "n" => 1),
            "m/s"   => array("a" => 1, "b" => 0, "n" => 1),
            "ft/s"  => array("a" => 3.28, "b" => 0, "n" => 1),
            "mph"   => array("a" => 2.24, "b" => 0, "n" => 1),
            "knots" => array("a" => 1.94, "b" => 0, "n" => 1),
        ),
        "ft/s" => array(
            "km/h" => array("a" => 1.097, "b" => 0, "n" => 1),
            "m/s" => array("a" => 0.305, "b" => 0, "n" => 1),
            "ft/s" => array("a" => 1, "b" => 0, "n" => 1),
            "mph" => array("a" => 0.682, "b" => 0, "n" => 1),
            "knots" => array("a" => 0.592, "b" => 0, "n" => 1),
        ),
        "mph" => array(
            "km/h" => array("a" => 1.609, "b" => 0, "n" => 1),
            "m/s" => array("a" => 0.45, "b" => 0, "n" => 1),
            "ft/s" => array("a" => 1.47, "b" => 0, "n" => 1),
            "mph" => array("a" => 1, "b" => 0, "n" => 1),
            "knots" => array("a" => 0.869, "b" => 0, "n" => 1),
        ),
        "knots" => array(
            "km/h" => array("a" => 1.85, "b" => 0, "n" => 1),
            "m/s" => array("a" => 0.51, "b" => 0, "n" => 1),
            "ft/s" => array("a" => 1.69, "b" => 0, "n" => 1),
            "mph" => array("a" => 1.15, "b" => 0, "n" => 1),
            "knots" => array("a" => 1, "b" => 0, "n" => 1),
        ),
        // end Speed Units

        #------Density Units---##
        "kg/m 3" => array(
            "kg/m 3" => array("a" => 1, "b" => 0, "n" => 1),
            "g/cm 3" => array("a" => 0.001, "b" => 0, "n" => 1),
        ),
        "g/cm 3" => array(
            "kg/m 3" => array("a" => 1000, "b" => 0, "n" => 1),
            "g/cm 3" => array("a" => 1, "b" => 0, "n" => 1),
        ),
        #------END Density Units---##

           #------Volume Units ------#

    "m3" => array(
                    "m3"     => array("a" => 1, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 1000, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 1000000, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 61023.7, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 35.3147, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 1.30795, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 1000, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 10000, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 100000, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 33814, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 264.172, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 6.39, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 2113.38, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 1000000, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 1056.69, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 1*1000000000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 1, "b" => 0, "n" => 1),
                ),
    
    "dm3" => array(
                    "m3"     => array("a" => 0.001, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 1, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 1000, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 61.0237, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.0353147, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 0.00130795, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 1, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 10, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 100, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 33.814, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.264172, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.0062898105697751, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 2.11338, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 1000, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 1.05669, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 1000000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.001, "b" => 0, "n" => 1),
                ),

    "cm3" => array(
                    "m3"     => array("a" => 1/1000000, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 0.001, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 1, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 0.0610237, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 3.5315/100000, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 1.30795/1000000, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 0.001, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 0.01, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 0.1, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 0.033814, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.000264172, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 6.0/1000000, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 0.00211338, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 1, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 0.00105669, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 1000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 1/1000000, "b" => 0, "n" => 1),
                ),

    "in3" => array(
                    "m3"     => array("a" => 1.63871/100000, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 0.0163871, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 16.3871, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 1, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.000578704, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 2.14335/100000, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 0.0163871, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 0.163871, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 1.63871, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 0.554113, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.004329, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.000137, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 0.034632, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 16.3871, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 0.017316, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 16387.1, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 1.63871/100000, "b" => 0, "n" => 1),
                ),

    "ft3" => array(
                    "m3"     => array("a" => 0.0283168, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 28.3168, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 28316.8, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 1728, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 1, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 0.037037, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 28.3168, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 283.168, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 2831.68, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 957.506, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 7.48052, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.18, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 59.8442, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 28316.8, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 29.9221, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 28316846.6, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.0283168, "b" => 0, "n" => 1),
                ),

    "yd3" => array(
                    "m3"     => array("a" => 0.764555, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 764.555, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 764555, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 46656, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 27, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 1, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 764.555, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 7645.55, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 76455.5, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 25852.7, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 201.974, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 4.8089, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 1615.79, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 764555, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 807.896, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 7.646*100000000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.764555, "b" => 0, "n" => 1),
                ),

    "liter" => array(
                    "m3"     => array("a" => 0.001, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 1, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 1000, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 61.0237, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.0353147, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 0.00130795, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 1, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 10, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 100, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 33.814, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.264172, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.00628981, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 2.11338, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 1000, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 1.05669, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 1000000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.001, "b" => 0, "n" => 1),
                ),

    "dl" => array(
                    "m3"     => array("a" => 0.0001, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 0.1, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 100, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 6.10237, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.00353147, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 0.000130795, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 0.1, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 1, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 10, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 3.3814, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.0264172, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.00062898105697751, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 0.211338, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 100, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 0.105669, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 100000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.0001, "b" => 0, "n" => 1),
                ),

    "cl" => array(
                    "m3"     => array("a" => 1/100000, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 0.01, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 10, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 0.610237, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.000353147, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 1.30795/100000, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 0.01, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 0.1, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 1, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 0.33814, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.00264172, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 8.4/100000, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 0.0211338, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 10, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 0.0105669, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 10000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 1/100000, "b" => 0, "n" => 1),
                ),

    "fl oz" => array(
                    "m3"     => array("a" => 2.95735/100000, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 0.0295735, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 29.5735, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 1.80469, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.00104438, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 3.86807/100000, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 0.0295735, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 0.295735, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 2.95735, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 1, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.0078125, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.00019, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 0.0625, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 29.5735, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 0.03125, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 29573.5, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 2.95735/100000, "b" => 0, "n" => 1),
                ),

    "ga" => array(
                    "m3"     => array("a" => 0.00378541, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 3.78541, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 3785.41, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 231, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.133681, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 0.00495113, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 3.78541, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 37.8541, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 378.541, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 128, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 1, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.0238095, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 8, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 3785.41, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 4, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 3.785*1000000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.00378541, "b" => 0, "n" => 1),
                ),

    "barrel" => array(
                    "m3"     => array("a" => 0.1589873, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 158.9873, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 158987.3, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 9702.0003095124, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 5.6145835124493, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 0.21, "b" => 0, "n" => 1),//done till here
                    "liter"  => array("a" => 158.9873, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 1589.873, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 15898.73, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 5299.577, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 42.01, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 1, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 336.001, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 158987.3, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 126, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 158987294.93, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.1589873, "b" => 0, "n" => 1),
                ),

    "pint" => array(
                    "m3"     => array("a" => 0.000473176, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 0.473176, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 473.176, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 28.875, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.0167101, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 0.000618891, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 0.473176, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 4.73176, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 47.3176, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 16, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.125, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.00297619, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 1, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 473.176, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 0.5, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 473176, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.000473176, "b" => 0, "n" => 1),
                ),

    "ml" => array(
                    "m3"     => array("a" => 1/1000000, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 0.001, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 1, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 0.0610237, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 3.53147/100000, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 1.30795/1000000, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 0.001, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 0.01, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 0.1, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 0.033814, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.000264172, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.00000628981, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 0.00211338, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 1, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 0.00105669, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 1000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 1/1000000, "b" => 0, "n" => 1),
                ),

    "quarts" => array(
                    "m3"     => array("a" => 0.000946353, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 0.946353, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 946.353, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 57.75, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 0.0334201, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 0.00123778, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 0.946353, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 9.46353, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 94.6353, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 32, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 0.25, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 0.0060, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 2, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 946.353, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 1, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 946353, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 0.000946353, "b" => 0, "n" => 1),
                ),

    "mm3" => array(
                    "m3"     => array("a" => 1/1000000000, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 1/1000000, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 0.001, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 6.1024/100000, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 3.5315/100000000, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 1.30795/1000000000, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 1/1000000, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 1/100000, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 0.0001, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 3.3814/100000, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 2.64172/10000000, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 6.289814/1000000000, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 2.11338/1000000, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 0.001, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 1.05669/1000000, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 1, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 1/1000000000, "b" => 0, "n" => 1),
                ),

    "kl" => array(
                    "m3"     => array("a" => 1, "b" => 0, "n" => 1),
                    "dm3"    => array("a" => 1000, "b" => 0, "n" => 1),
                    "cm3"    => array("a" => 1000000, "b" => 0, "n" => 1),
                    "in3"    => array("a" => 61023.7, "b" => 0, "n" => 1),
                    "ft3"    => array("a" => 35.3147, "b" => 0, "n" => 1),
                    "yd3"    => array("a" => 1.30795, "b" => 0, "n" => 1),
                    "liter"  => array("a" => 1000, "b" => 0, "n" => 1),
                    "dl"     => array("a" => 10000, "b" => 0, "n" => 1),
                    "cl"     => array("a" => 100000, "b" => 0, "n" => 1),
                    "fl oz"  => array("a" => 33814, "b" => 0, "n" => 1),
                    "ga"     => array("a" => 264.172, "b" => 0, "n" => 1),
                    "barrel" => array("a" => 6.39, "b" => 0, "n" => 1),
                    "pint"   => array("a" => 2113.38, "b" => 0, "n" => 1),
                    "ml"     => array("a" => 1000000, "b" => 0, "n" => 1),
                    "quarts" => array("a" => 1056.69, "b" => 0, "n" => 1),
                    "mm3"    => array("a" => 1*1000000000, "b" => 0, "n" => 1),
                    "kl"     => array("a" => 1, "b" => 0, "n" => 1),
                ),

        #------End Volume Units ------#

        // Lenght to Others
        
        "decam" => array(
            "km"    => array("a" => 1, "b" => 0, "n" => 1),
            "dm"    => array("a" => 10000, "b" => 0, "n" => 1), //decimeter
            "cm"    => array("a" => 100000, "b" => 0, "n" => 1), //centimeter
            "mm"    => array("a" => 1000000, "b" => 0, "n" => 1), //milimeter
            "microm"    => array("a" => 1000000000, "b" => 0, "n" => 1), //micrometer
            "nm"    => array("a" => 1000000000000, "b" => 0, "n" => 1), //nanometer
            "pm"    => array("a" => 10000000000000, "b" => 0, "n" => 1), //picometer
            "inch"  => array("a" => 393.701, "b" => 0, "n" => 1), //inch
            "ft"    => array("a" => 32.8084, "b" => 0, "n" => 1), //foot
            "yd"    => array("a" => 10.9361, "b" => 0, "n" => 1), //yard
            "mi"    => array("a" => 0.00621371, "b" => 0, "n" => 1), //mile
            "hand"     => array("a" => 98.4252, "b" => 0, "n" => 1), //hand
            "ly"    => array("a" => 0.000000000000001057, "b" => 0, "n" => 1), //lightyear
            "au"    => array("a" => 0.0000000000668459e-11, "b" => 0, "n" => 1), //astronomical unit
            "pc"    => array("a" => 0.0000000000000000324, "b" => 0, "n" => 1), //parsec
            "m"     => array("a" => 10, "b" => 0, "n" => 1),
            "decam"     => array("a" => 1, "b" => 0, "n" => 1),
        ),
        "km" => array(
            "km"    => array("a" => 1, "b" => 0, "n" => 1),
            "dm"    => array("a" => 10000, "b" => 0, "n" => 1), //decimeter
            "cm"    => array("a" => 100000, "b" => 0, "n" => 1), //centimeter
            "mm"    => array("a" => 1000000, "b" => 0, "n" => 1), //milimeter
            "microm"    => array("a" => 1000000000, "b" => 0, "n" => 1), //micrometer
            "nm"    => array("a" => 1000000000000, "b" => 0, "n" => 1), //nanometer
            "pm"    => array("a" => 1000000000000000, "b" => 0, "n" => 1), //picometer
            "inch"  => array("a" => 39370.1, "b" => 0, "n" => 1), //inch
            "ft"    => array("a" => 3280.84, "b" => 0, "n" => 1), //foot
            "yd"    => array("a" => 1093.61, "b" => 0, "n" => 1), //yard
            "mi"    => array("a" => 0.621371, "b" => 0, "n" => 1), //mile
            "hand"     => array("a" => 9842.52, "b" => 0, "n" => 1), //hand
            "ly"    => array("a" => 0.0000000000001057, "b" => 0, "n" => 1), //lightyear
            "au"    => array("a" => 0.0000000067, "b" => 0, "n" => 1), //astronomical unit
            "pc"    => array("a" => 0.000000000000032, "b" => 0, "n" => 1), //parsec
            "m"     => array("a" => 1000, "b" => 0, "n" => 1),
            "decam"     => array("a" => 100, "b" => 0, "n" => 1),
        ),
        "dm" => array(
            "km"    => array("a" => 0.0001, "b" => 0, "n" => 1),
            "dm"    => array("a" => 1, "b" => 0, "n" => 1),
            "cm"    => array("a" => 10, "b" => 0, "n" => 1),
            "mm"    => array("a" => 100, "b" => 0, "n" => 1),
            "microm"    => array("a" => 100000, "b" => 0, "n" => 1),
            "nm"    => array("a" => 100000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 100000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 3.93701, "b" => 0, "n" => 1),
            "ft"    => array("a" => 0.3280841666667, "b" => 0, "n" => 1),
            "yd"    => array("a" => 0.109361388888899, "b" => 0, "n" => 1),
            "mi"    => array("a" => 0.000062137152777784, "b" => 0, "n" => 1),
            "hand"     => array("a" => 0.984252, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1.057/100000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 0.000000000000668459, "b" => 0, "n" => 1),
            "pc"    => array("a" => 3.24078/1000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 0.1, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.01, "b" => 0, "n" => 1),
        ),
        "cm" => array(
            "km"    => array("a" => 0.00001, "b" => 0, "n" => 1),
            "dm"    => array("a" => 0.1, "b" => 0, "n" => 1),
            "cm"    => array("a" => 1, "b" => 0, "n" => 1),
            "mm"    => array("a" => 10, "b" => 0, "n" => 1),
            "microm"    => array("a" => 10000, "b" => 0, "n" => 1),
            "nm"    => array("a" => 10000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 10000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 0.393701, "b" => 0, "n" => 1),
            "ft"    => array("a" => 0.03280841666667, "b" => 0, "n" => 1),
            "yd"    => array("a" => 0.0109361388888899, "b" => 0, "n" => 1),
            "mi"    => array("a" => 0.0000062137152777784, "b" => 0, "n" => 1),
            "hand"     => array("a" => 0.984252, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1.057/1000000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 6.68459/100000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 3.24078/10000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 0.01, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.001, "b" => 0, "n" => 1),
        ),
        "mm" => array(
            "km"    => array("a" => 0.000001, "b" => 0, "n" => 1),
            "dm"    => array("a" => 0.01, "b" => 0, "n" => 1),
            "cm"    => array("a" => 0.1, "b" => 0, "n" => 1),
            "mm"    => array("a" => 1, "b" => 0, "n" => 1),
            "microm"    => array("a" => 1000, "b" => 0, "n" => 1),
            "nm"    => array("a" => 1000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 1000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 0.03937008, "b" => 0, "n" => 1),
            "ft"    => array("a" => 0.003280841666667, "b" => 0, "n" => 1),
            "yd"    => array("a" => 0.00109361333333331, "b" => 0, "n" => 1),
            "mi"    => array("a" => 0.0000006213712121212, "b" => 0, "n" => 1),
            "hand"     => array("a" => 0.984252, "b" => 0, "n" => 1),
            "ly"    => array("a" => 0.0000000000000000106, "b" => 0, "n" => 1),
            "au"    => array("a" => 0.000000000000668459, "b" => 0, "n" => 1),
            "pc"    => array("a" => 3.24078/100000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 0.001, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.0001, "b" => 0, "n" => 1),
        ),
        "microm" => array(
            "km"    => array("a" => 0.000000001000000431, "b" => 0, "n" => 1),
            "dm"    => array("a" => 0.00004, "b" => 0, "n" => 1),
            "cm"    => array("a" => 0.0001000000431, "b" => 0, "n" => 1),
            "mm"    => array("a" => 0.00100000043099999, "b" => 0, "n" => 1),
            "microm"    => array("a" => 1, "b" => 0, "n" => 1),
            "nm"    => array("a" => 1000.00043099999, "b" => 0, "n" => 1),
            "pm"    => array("a" => 3.24078/100000000000000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 0.0000393700957086614, "b" => 0, "n" => 1),
            "ft"    => array("a" => 3.28084130905511/1000000, "b" => 0, "n" => 1),
            "yd"    => array("a" => 1.09361376968503/1000000, "b" => 0, "n" => 1),
            "mi"    => array("a" => 6.21371460048317/10000000000, "b" => 0, "n" => 1),
            "hand"     => array("a" => 0.00000984252, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1.057/10000000000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 6.68459/1000000000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 3.240779999366/100000000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 0.000001, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.00000001, "b" => 0, "n" => 1),
        ),
        "nm" => array(
            "km"    => array("a" => 0.000000000001, "b" => 0, "n" => 1),
            "dm"    => array("a" => 0.00000001, "b" => 0, "n" => 1),
            "cm"    => array("a" => 1/10000000, "b" => 0, "n" => 1),
            "mm"    => array("a" => 1/1000000, "b" => 0, "n" => 1),
            "microm"    => array("a" => 0.001, "b" => 0, "n" => 1),
            "nm"    => array("a" => 1, "b" => 0, "n" => 1),
            "pm"    => array("a" => 1000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 3.937/100000000, "b" => 0, "n" => 1),
            "ft"    => array("a" => 3.280833333/1000000000, "b" => 0, "n" => 1),
            "yd"    => array("a" => 1.0936/1000000000, "b" => 0, "n" => 1),
            "mi"    => array("a" => 6.21363636364/10000000000000, "b" => 0, "n" => 1),
            "hand"     => array("a" => 9.84252/1000000000, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1.057/10000000000000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 6.68459/1000000000000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 3.24078/100000000000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 1.000000219/1000000000, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.00000000001, "b" => 0, "n" => 1),
        ),
        "pm" => array(
            "km"    => array("a" => 1/1000000000000000, "b" => 0, "n" => 1),
            "dm"    => array("a" => 1/100000000000, "b" => 0, "n" => 1),
            "cm"    => array("a" => 1/10000000000, "b" => 0, "n" => 1),
            "mm"    => array("a" => 1/1000000000, "b" => 0, "n" => 1),
            "microm"    => array("a" => 1/1000000, "b" => 0, "n" => 1),
            "nm"    => array("a" => 0.001, "b" => 0, "n" => 1),
            "pm"    => array("a" => 1, "b" => 0, "n" => 1),
            "inch"  => array("a" => 3.93696/100000000000, "b" => 0, "n" => 1),
            "ft"    => array("a" => 3.2808/1000000000000, "b" => 0, "n" => 1),
            "yd"    => array("a" => 1.0936/1000000000000, "b" => 0, "n" => 1),
            "mi"    => array("a" => 6.21363636364/10, "b" => 0, "n" => 1),
            "hand"     => array("a" => 9.84252/1000000000000, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1.057/10000000000000000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 6.68459/1000000000000000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 3.24078/100000000000000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 1/1000000000000, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.00000000000001, "b" => 0, "n" => 1),
        ),
        "inch" => array(
            "km"    => array("a" => 2.54/100000, "b" => 0, "n" => 1),
            "dm"    => array("a" => 0.254, "b" => 0, "n" => 1),
            "cm"    => array("a" => 2.54, "b" => 0, "n" => 1),
            "mm"    => array("a" => 25.4, "b" => 0, "n" => 1),
            "microm"    => array("a" => 25400, "b" => 0, "n" => 1),
            "nm"    => array("a" => 2.54*10000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 2.54*10000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 1, "b" => 0, "n" => 1),
            "ft"    => array("a" => 0.0833333, "b" => 0, "n" => 1),
            "yd"    => array("a" => 0.0277777666667, "b" => 0, "n" => 1),
            "mi"    => array("a" => 1.578282196971590999/100000, "b" => 0, "n" => 1),
            "hand"     => array("a" => 0.25, "b" => 0, "n" => 1),
            "ly"    => array("a" => 2.68478/1000000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 1.69789/10000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 8.23158/10000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 0.0254000018542, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.00254, "b" => 0, "n" => 1),
        ),
        "ft" => array(
            "km"    => array("a" => 0.0003048, "b" => 0, "n" => 1),
            "dm"    => array("a" => 3.048, "b" => 0, "n" => 1),
            "cm"    => array("a" => 30.48, "b" => 0, "n" => 1),
            "mm"    => array("a" => 304.8, "b" => 0, "n" => 1),
            "microm"    => array("a" => 304800, "b" => 0, "n" => 1),
            "nm"    => array("a" => 3.048*100000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 3.048*100000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 12, "b" => 0, "n" => 1),
            "ft"    => array("a" => 1, "b" => 0, "n" => 1),
            "yd"    => array("a" => 0.333333, "b" => 0, "n" => 1),
            "mi"    => array("a" => 0.00018939375, "b" => 0, "n" => 1),
            "hand"     => array("a" => 3, "b" => 0, "n" => 1),
            "ly"    => array("a" => 3.22174/100000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 2.03746/1000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 9.8779/1000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 0.304800146304, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.03048, "b" => 0, "n" => 1),
        ),
        "yd" => array(
            "km"    => array("a" => 0.0009144, "b" => 0, "n" => 1),
            "dm"    => array("a" => 9.144, "b" => 0, "n" => 1),
            "cm"    => array("a" => 91.44, "b" => 0, "n" => 1),
            "mm"    => array("a" => 914.4, "b" => 0, "n" => 1),
            "microm"    => array("a" => 914400, "b" => 0, "n" => 1),
            "nm"    => array("a" => 9.144*100000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 9.144*100000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 36, "b" => 0, "n" => 1),
            "ft"    => array("a" => 3, "b" => 0, "n" => 1),
            "yd"    => array("a" => 1, "b" => 0, "n" => 1),
            "mi"    => array("a" => 0.000568182, "b" => 0, "n" => 1),
            "hand"     => array("a" => 9, "b" => 0, "n" => 1),
            "ly"    => array("a" => 9.66522/100000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 6.11239/1000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 2.96337/100000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 0.9144, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.09144, "b" => 0, "n" => 1),
        ),
        "mi" => array(
            "km"    => array("a" => 1.60934, "b" => 0, "n" => 1),
            "dm"    => array("a" => 16093.4, "b" => 0, "n" => 1),
            "cm"    => array("a" => 160934, "b" => 0, "n" => 1),
            "mm"    => array("a" => 1609343.99983907, "b" => 0, "n" => 1),
            "microm"    => array("a" => 1609343999.8390700817, "b" => 0, "n" => 1),
            "nm"    => array("a" => 1609343999839.0698242, "b" => 0, "n" => 1),
            "pm"    => array("a" => 1609343999839069.8242, "b" => 0, "n" => 1),
            "inch"  => array("a" => 63360, "b" => 0, "n" => 1),
            "ft"    => array("a" => 5280, "b" => 0, "n" => 1),
            "yd"    => array("a" => 1760, "b" => 0, "n" => 1),
            "mi"    => array("a" => 1, "b" => 0, "n" => 1),
            "hand"     => array("a" => 15840, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1.70108/10000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 1.07578/100000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 5.2155/100000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 1609.34, "b" => 0, "n" => 1),
            "decam"     => array("a" => 160.934, "b" => 0, "n" => 1),
        ),
        "h" => array(
            "km"    => array("a" => 0.0001016, "b" => 0, "n" => 1),
            "dm"    => array("a" => 1.016, "b" => 0, "n" => 1),
            "cm"    => array("a" => 10.16, "b" => 0, "n" => 1),
            "mm"    => array("a" => 101.6, "b" => 0, "n" => 1),
            "microm"    => array("a" => 101600, "b" => 0, "n" => 1),
            "nm"    => array("a" => 1.016*100000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 1.016*100000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 4, "b" => 0, "n" => 1),
            "ft"    => array("a" => 0.333333, "b" => 0, "n" => 1),
            "yd"    => array("a" => 0.111111, "b" => 0, "n" => 1),
            "mi"    => array("a" => 6.313125/100000, "b" => 0, "n" => 1),
            "hand"     => array("a" => 1, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1.07391/100000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 6.79154/10000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 3.29263/1000000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 0.1016, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.01016, "b" => 0, "n" => 1),
        ),
        "ly" => array(
            "km"    => array("a" => 9460730567188, "b" => 0, "n" => 1),
            "dm"    => array("a" => 94607304725810000, "b" => 0, "n" => 1),
            "cm"    => array("a" => 946073056718800, "b" => 0, "n" => 1),
            "mm"    => array("a" => 9460730567188002816, "b" => 0, "n" => 1),
            "microm"    => array("a" => 9.46073056718800447*1000000000000000000000, "b" => 0, "n" => 1),
            "nm"    => array("a" => 9.46073056718800375*100000000000000000000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 9.46073056718800375*1000000000000000000000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 372469707369606464, "b" => 0, "n" => 1),
            "ft"    => array("a" => 31039142280800536, "b" => 0, "n" => 1),
            "yd"    => array("a" => 10346380760266846, "b" => 0, "n" => 1),
            "mi"    => array("a" => 5878625431969.7988281, "b" => 0, "n" => 1),
            "hand"     => array("a" => 9.312*10000000000000000, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1, "b" => 0, "n" => 1),
            "au"    => array("a" => 63241.1, "b" => 0, "n" => 1),
            "pc"    => array("a" => 0.306601, "b" => 0, "n" => 1),
            "m"     => array("a" => 9.461*1000000000000000, "b" => 0, "n" => 1),
            "decam"     => array("a" => 946100000000000, "b" => 0, "n" => 1),
        ),
        "m" => array(
            "km"    => array("a" => 0.001, "b" => 0, "n" => 1),
            "dm"    => array("a" => 10, "b" => 0, "n" => 1),
            "cm"    => array("a" => 100, "b" => 0, "n" => 1),
            "mm"    => array("a" => 1000, "b" => 0, "n" => 1),
            "microm"    => array("a" => 1000000, "b" => 0, "n" => 1),
            "nm"    => array("a" => 1000000000, "b" => 0, "n" => 1),
            "pm"    => array("a" => 1000000000000, "b" => 0, "n" => 1),
            "inch"  => array("a" => 39.37008, "b" => 0, "n" => 1),
            "ft"    => array("a" => 3.28084, "b" => 0, "n" => 1),
            "yd"    => array("a" => 1.093613, "b" => 0, "n" => 1),
            "mi"    => array("a" => 0.000621, "b" => 0, "n" => 1),
            "hand"     => array("a" => 9.84252, "b" => 0, "n" => 1),
            "ly"    => array("a" => 1.057/10000000000000000, "b" => 0, "n" => 1),
            "au"    => array("a" => 6.68459/1000000000000, "b" => 0, "n" => 1),
            "pc"    => array("a" => 3.24078/100000000000000000, "b" => 0, "n" => 1),
            "m"     => array("a" => 1, "b" => 0, "n" => 1),
            "decam"     => array("a" => 0.1, "b" => 0, "n" => 1),
        ),
        // END Lenght to Others

        //  Angle Units
        "deg" => array(
            "deg"    => array("a" => 1, "b" => 0, "n" => 1),
            "rad"   => array("a" => 0.0174532925199433, "b" => 0, "n" => 1),
            "min"   => array("a" => 60, "b" => 0, "n" => 1),
            "sec"   => array("a" => 3600, "b" => 0, "n" => 1),
            "grad"   => array("a" => 1.11111, "b" => 0, "n" => 1),
        ),
        "rad" => array(
            "deg"    => array("a" => 57.2957212376871, "b" => 0, "n" => 1),
            "rad"   => array("a" => 1, "b" => 0, "n" => 1),
            "min"   => array("a" => 3437.7432742494074773, "b" => 0, "n" => 1),
            "sec"   => array("a" => 206264.59645506550441, "b" => 0, "n" => 1),
            "grad"   => array("a" => 63.6619135721162408, "b" => 0, "n" => 1),
        ),
        "min" => array(
            "deg"    => array("a" => 0.016666649715057289816, "b" => 0, "n" => 1),
            "rad"   => array("a" => 0.00029088791280360286762, "b" => 0, "n" => 1),
            "min"   => array("a" => 1, "b" => 0, "n" => 1),
            "sec"   => array("a" => 60, "b" => 0, "n" => 1),
            "grad"   => array("a" => 0.0185185, "b" => 0, "n" => 1),
        ),
        "sec" => array(
            "deg"    => array("a" => 0.000277778, "b" => 0, "n" => 1),
            "rad"   => array("a" => 4.848140689593013602/1000000, "b" => 0, "n" => 1),
            "min"   => array("a" => 0.016666679999943499746, "b" => 0, "n" => 1),
            "sec"   => array("a" => 1, "b" => 0, "n" => 1),
            "grad"   => array("a" => 0.000308642227486340126, "b" => 0, "n" => 1),
        ),
        "grad" => array(
            "deg"    => array("a" => 0.90000070464726522612, "b" => 0, "n" => 1),
            "rad"   => array("a" => 0.01570797556632484368, "b" => 0, "n" => 1),
            "min"   => array("a" => 54.000042278650276728, "b" => 0, "n" => 1),
            "sec"   => array("a" => 3240.0025367206039846, "b" => 0, "n" => 1),
            "grad"   => array("a" => 1, "b" => 0, "n" => 1),
        ),
        // END Angle Units 

        // Area Units
        "m2" => array(
            "m2"    => array("a"  => 1, "b" => 0, "n" => 1),
            "km2"   => array("a" => 1/1000000, "b" => 0, "n" => 1),
            "ft2"   => array("a" => 10.7639, "b" => 0, "n" => 1),
            "ha"    => array("a" => 9.999990323/100000, "b" => 0, "n" => 1),
            "acre"  => array("a" => 0.000247105, "b" => 0, "n" => 1),
            'in2'   => array("a" => 1550, "b" => 0, "n" => 1),
            'yd2'   => array("a" => 1.19599, "b" => 0, "n" => 1),
            'mi2'   => array("a" => 3.86102/10000000, "b" => 0, "n" => 1),
        ),
        "km2" => array(
            "m2"    => array("a" => 1*1000000, "b" => 0, "n" => 1),
            "km2"   => array("a" => 1, "b" => 0, "n" => 1),
            "ft2"   => array("a" => 1.076*10000000, "b" => 0, "n" => 1),
            "ha"    => array("a" => 100, "b" => 0, "n" => 1),
            "acre"  => array("a" => 247.105, "b" => 0, "n" => 1),
            'in2'   => array("a" => 1.55*1000000000, "b" => 0, "n" => 1),
            'yd2'   => array("a" => 1.196*1000000, "b" => 0, "n" => 1),
            'mi2'   => array("a" => 0.386102, "b" => 0, "n" => 1),
        ),
        "ft2" => array(
            "m2"    => array("a" => 0.092903682888999639111, "b" => 0, "n" => 1),
            "km2"   => array("a" => 9.2903682888999638/100000000, "b" => 0, "n" => 1),
            "ft2"   => array("a" => 1, "b" => 0, "n" => 1),
            "ha"    => array("a" => 9.2903682889/1000000, "b" => 0, "n" => 1),
            "acre"  => array("a" => 2.2957/100000, "b" => 0, "n" => 1),
            'in2'   => array("a" => 144, "b" => 0, "n" => 1),
            'yd2'   => array("a" => 0.111111, "b" => 0, "n" => 1),
            'mi2'   => array("a" => 3.587002841/100000000, "b" => 0, "n" => 1),
        ),
        "ha" => array(
            "m2"    => array("a" => 10000, "b" => 0, "n" => 1),
            "km2"   => array("a" => 0.01, "b" => 0, "n" => 1),
            "ft2"   => array("a" => 107639, "b" => 0, "n" => 1),
            "ha"    => array("a" => 1, "b" => 0, "n" => 1),
            "acre"  => array("a" => 2.47105, "b" => 0, "n" => 1),
            'in2'   => array("a" => 1.55*10000000, "b" => 0, "n" => 1),
            'yd2'   => array("a" => 11959.895552000595, "b" => 0, "n" => 1),
            'mi2'   => array("a" => 0.00386102, "b" => 0, "n" => 1),
        ),
        "acre" => array(
            "m2"    => array("a" => 4046.86, "b" => 0, "n" => 1),
            "km2"   => array("a" => 0.00404686, "b" => 0, "n" => 1),
            "ft2"   => array("a" => 43560, "b" => 0, "n" => 1),
            "ha"    => array("a" => 0.404686, "b" => 0, "n" => 1),
            "acre"  => array("a" => 2.2957/100000, "b" => 0, "n" => 1),
            'in2'   => array("a" => 6272627.4547, "b" => 0, "n" => 1),
            'yd2'   => array("a" => 4839.9903199845657582, "b" => 0, "n" => 1),
            'mi2'   => array("a" => 0.0015625, "b" => 0, "n" => 1),
        ),
        "in2" => array(
            "m2"    => array("a" => 0.00064516, "b" => 0, "n" => 1),
            "km2"   => array("a" => 6.4516/10000000000, "b" => 0, "n" => 1),
            "ft2"   => array("a" => 0.00694444, "b" => 0, "n" => 1),
            "ha"    => array("a" => 6.4516/100000000, "b" => 0, "n" => 1),
            "acre"  => array("a" => 1.5942/10000000, "b" => 0, "n" => 1),
            'in2'   => array("a" => 1, "b" => 0, "n" => 1),
            'yd2'   => array("a" => 0.000771605, "b" => 0, "n" => 1),
            'mi2'   => array("a" => 2.491/10000000000, "b" => 0, "n" => 1),
        ),
        "yd2" => array(
            "m2"    => array("a" => 0.836127, "b" => 0, "n" => 1),
            "km2"   => array("a" => 8.3613/10000000, "b" => 0, "n" => 1),
            "ft2"   => array("a" => 9, "b" => 0, "n" => 1),
            "ha"    => array("a" => 8.3613/100000, "b" => 0, "n" => 1),
            "acre"  => array("a" => 0.000206612, "b" => 0, "n" => 1),
            'in2'   => array("a" => 1296, "b" => 0, "n" => 1),
            'yd2'   => array("a" => 1, "b" => 0, "n" => 1),
            'mi2'   => array("a" => 3.2283/10000000, "b" => 0, "n" => 1),
        ),
        "mi2" => array(
            "m2"    => array("a" => 2.59*1000000, "b" => 0, "n" => 1),
            "km2"   => array("a" => 2.58999, "b" => 0, "n" => 1),
            "ft2"   => array("a" => 2.788*10000000, "b" => 0, "n" => 1),
            "ha"    => array("a" => 258.999, "b" => 0, "n" => 1),
            "acre"  => array("a" => 640, "b" => 0, "n" => 1),
            'in2'   => array("a" => 4.014*1000000000, "b" => 0, "n" => 1),
            'yd2'   => array("a" => 3.098*1000000, "b" => 0, "n" => 1),
            'mi2'   => array("a" => 1, "b" => 0, "n" => 1),
        ),
        #------END Area to other----#

        // Frequency units
        "hz" => array(
            "ghz"   => array("a" => 1.00/1000000000, "b" => 0, "n" => 1),
            "mhz"   => array("a" => 0.000001, "b" => 0, "n" => 1),
            "khz"   => array("a" => 0.001, "b" => 0, "n" => 1),
            "hz"    => array("a" => 1, "b" => 0, "n" => 1),
        ),
        "ghz" => array(
            "ghz"   => array("a" => 1, "b" => 0, "n" => 1),
            "mhz"   => array("a" => 1000, "b" => 0, "n" => 1),
            "khz"   => array("a" => 1000000, "b" => 0, "n" => 1),
            "hz"    => array("a" => 1000000000, "b" => 0, "n" => 1),
        ),
        "mhz" => array(
            "ghz"   => array("a" => 0.001, "b" => 0, "n" => 1),
            "mhz"   => array("a" => 1, "b" => 0, "n" => 1),
            "khz"   => array("a" => 1000, "b" => 0, "n" => 1),
            "hz"    => array("a" => 1000000, "b" => 0, "n" => 1),
        ),
        "khz" => array(
            "ghz"   => array("a" => 1/1000000, "b" => 0, "n" => 1),
            "mhz"   => array("a" => 0.001, "b" => 0, "n" => 1),
            "khz"   => array("a" => 1, "b" => 0, "n" => 1),
            "hz"    => array("a" => 1000, "b" => 0, "n" => 1),
        ),
        // END Frequency units

        #------Tempreture to other----#
        "cel" => array(
            "kel"     => array("a" => 1, "b" => 273.15, "n" => 1),
            "feh"     => array("a" => 1.8, "b" => 32, "n" => 1),
            "cel"     => array("a" => 1, "b" => 0, "n" => 1),
            "rank"  => array("a" => 1.8, "b" => 491.67, "n" => 1),
        ),
        "kel" => array(
            "cel"     => array("a" => 1, "b" => -273.15, "n" => 1),
            "kel"     => array("a" => 1, "b" => 0, "n" => 1),
            "feh"     => array("a" => 1.8, "b" => -459.67, "n" => 1),
            "rank"  => array("a" => 1.8, "b" => 0, "n" => 1),
        ),
        "feh" => array(
            "cel"     => array("a" => 0.555555555555556, "b" => -17.7777777777778, "n" => 1),
            "feh"     => array("a" => 1, "b" => 0, "n" => 1),
            "kel"     => array("a" => 0.555555555555556, "b" => 255.372222222, "n" => 1),
            "rank"  => array("a" => 1, "b" => 459.67, "n" => 1),
        ),
        "rank" => array(
            "cel"     => array("a" => 0.555555555555556, "b" => -273.15, "n" => 1),
            "feh"     => array("a" => 1, "b" => -459.97, "n" => 1),
            "kel"     => array("a" => 0.555555555555556, "b" => 0, "n" => 1),
            "rank"  => array("a" => 1, "b" => 0, "n" => 1),
        ),
        #------END Tempreture to other----#

        #------Volt to other----#
        "v" => array(
            "v"     => array("a" => 1, "b" => 0, "n" => 1),
            "mv"    => array("a" => 1000, "b" => 0, "n" => 1),
            "microv"    => array("a" => 1000000, "b" => 0, "n" => 1),
            "kv"    => array("a" => 0.001, "b" => 0, "n" => 1),
        ),
        "mv" => array(//milivolt
            "v"     => array("a" => 0.001, "b" => 0, "n" => 1),
            "mv"    => array("a" => 1, "b" => 0, "n" => 1),
            "microv"    => array("a" => 1000, "b" => 0, "n" => 1),
            "kv"    => array("a" => 0.000001, "b" => 0, "n" => 1),
        ),
        "microv" => array(
            "v"     => array("a" => 0.000001, "b" => 0, "n" => 1),
            "mv"    => array("a" => 0.001, "b" => 0, "n" => 1),
            "microv"    => array("a" => 1, "b" => 0, "n" => 1),
            "kv"    => array("a" => 0.000000001, "b" => 0, "n" => 1),
        ),
        "kv" => array(
            "v"     => array("a" => 1000, "b" => 0, "n" => 1),
            "mv"    => array("a" => 1000000, "b" => 0, "n" => 1),
            "microv"    => array("a" => 1000000000, "b" => 0, "n" => 1),
            "kv"    => array("a" => 1, "b" => 0, "n" => 1),
        ),
        #------End Volt to other----#

        #----Electric charge to---#
        "c" => array(
            "c" => array("a" => 1, "b" => 0, "n" => 1),
            "mc" => array("a" => 1000, "b" => 0, "n" => 1),
            "microc" => array("a" => 1000000, "b" => 0, "n" => 1),
            "nc" => array("a" => 1000000000, "b" => 0, "n" => 1),
        ),
        "mc" => array(
            "c" => array("a" => 0.001, "b" => 0, "n" => 1),
            "mc" => array("a" => 1, "b" => 0, "n" => 1),
            "microc" => array("a" => 1000, "b" => 0, "n" => 1),
            "nc" => array("a" => 1000000, "b" => 0, "n" => 1),
        ),
        "microc" => array(
            "c" => array("a" => 0.000001, "b" => 0, "n" => 1),
            "mc" => array("a" => 0.001, "b" => 0, "n" => 1),
            "microc" => array("a" => 1, "b" => 0, "n" => 1),
            "nc" => array("a" => 1000, "b" => 0, "n" => 1),
        ),
        "nc" => array(
            "c" => array("a" => 0.000000001, "b" => 0, "n" => 1),
            "mc" => array("a" => 0.000001, "b" => 0, "n" => 1),
            "microc" => array("a" => 0.001, "b" => 0, "n" => 1),
            "nc" => array("a" => 1, "b" => 0, "n" => 1),
        ),
        #----End Electric charge to---#

         #------Pressure Units---##
        "pa" => array(
            "bar" => array("a" => 0.00001, "b" => 0, "n" => 1),
            "atm" => array("a" => 0.00000986923266716, "b" => 0, "n" => 1),
            "torr" => array("a" => 0.007500616827042, "b" => 0, "n" => 1),
            "pa" => array("a" => 1, "b" => 0, "n" => 1),
            "psi" => array("a" => 0.000145038, "b" => 0, "n" => 1),
        ),
        "atm" => array(
            "bar" => array("a" => 1.01325, "b" => 0, "n" => 1),
            "pa" => array("a" => 101325, "b" => 0, "n" => 1),
            "torr" => array("a" => 760, "b" => 0, "n" => 1),
            "atm" => array("a" => 1, "b" => 0, "n" => 1),
            "psi" => array("a" => 14.6959, "b" => 0, "n" => 1),
        ),
        "bar" => array(
            "atm" => array("a" => 0.986923266716, "b" => 0, "n" => 1),
            "pa" => array("a" => 100000, "b" => 0, "n" => 1),
            "torr" => array("a" => 750.0616827042, "b" => 0, "n" => 1),
            "bar" => array("a" => 1, "b" => 0, "n" => 1),
            "psi" => array("a" => 14.5038, "b" => 0, "n" => 1),
        ),
        "torr" => array(
            "atm" => array("a" => 0.001315789473684, "b" => 0, "n" => 1),
            "bar" => array("a" => 0.001333223684211, "b" => 0, "n" => 1),
            "pa" => array("a" => 133.3223684211, "b" => 0, "n" => 1),
            "torr" => array("a" => 1, "b" => 0, "n" => 1),
            "psi" => array("a" => 0.0193368, "b" => 0, "n" => 1),
        ),
        "psi" => array(
            "atm" => array("a" => 0.068046, "b" => 0, "n" => 1),
            "bar" => array("a" => 0.0689476, "b" => 0, "n" => 1),
            "pa" => array("a" => 6894.76, "b" => 0, "n" => 1),
            "torr" => array("a" => 51.7149, "b" => 0, "n" => 1),
            "psi" => array("a" => 1, "b" => 0, "n" => 1),
        ),
        #------End Pressure Units---##

        #------Power Units---##
        "w" => array(
            "w" => array("a" => 1, "b" => 0, "n" => 1),
            "kw" => array("a" => 0.001, "b" => 0, "n" => 1),
        ),
        "kw" => array(
            "w" => array("a" => 1000, "b" => 0, "n" => 1),
            "kw" => array("a" => 1, "b" => 0, "n" => 1),
        ),
        
        #------END Power Units---##

        #------ digital information ------#

       "bit" => array(
                    "byte"  => array("a" =>0.125,"b"=>0 , "n" =>1 ),
                    "kb"    => array("a" =>0.000125, "b"=>0 , "n" =>1 ),
                    "mb"    => array("a" =>1.25/10000000  , "b"=>0 , "n" =>1 ),
                    "gb"    => array("a" =>1.25/10000000000  , "b"=>0 , "n" =>1 ),
                    "tb"    => array("a" =>1.25/10000000000000  , "b"=>0 , "n" =>1 ),
                    "bit"   => array("a" =>1, "b"=>0 , "n" =>1 ),
                    "pb"    => array("a" =>1.25/10000000000000000, "b"=>0 , "n" =>1 ),
                ),

       "byte" =>    array(
                        "bit"   => array("a" => 8 , "b"=>0, "n" =>1 ),
                        "kb"    => array("a" => 0.000977, "b"=>0 , "n" =>1 ),                                        
                        "mb"    => array("a" => 0.000001, "b"=>0 , "n" =>1 ),
                        "gb"    => array("a" => 0.000000001, "b"=>0 , "n" =>1 ),
                        "tb"    => array("a" => 0.000000000001, "b"=>0 , "n" =>1 ),
                        "byte"  => array("a" => 1, "b"=>0, "n" =>1 ),
                        "pb"    => array("a" => 1/1000000000000000, "b"=>0, "n" =>1 ),
                    ),

       "kb" => array(
                        "bit"   => array("a" =>8192, "b"=>0 , "n" =>1 ),
                        "byte"  => array("a" =>1024, "b"=>0 , "n" =>1 ),                                        
                        "mb"    => array("a" =>0.000977, "b"=>0 , "n" =>1 ),
                        "gb"    => array("a" =>0.000001, "b"=>0 , "n" =>1 ),
                        "tb"    => array("a" =>0.000000001, "b"=>0 , "n" =>1 ),
                        "kb"    => array("a" =>1, "b"=>0, "n" =>1 ),
                        "pb"    => array("a" =>1/1000000000000, "b"=>0, "n" =>1 ),
                        ),

        "mb" => array(
                        "bit"       => array("a" =>8388608, "b"=>0 , "n" =>1 ),
                        "byte"      => array("a" =>1048576 , "b"=>0 , "n" =>1 ),                                        
                        "kb"    => array("a" =>1024 , "b"=>0 , "n" =>1 ),
                        "gb"    => array("a" =>0.000977, "b"=>0 , "n" =>1 ),
                        "tb"    => array("a" =>0.000001, "b"=>0 , "n" =>1 ),
                        "mb"    => array("a" =>1, "b"=>0 , "n" =>1 ),
                        "pb"    => array("a" =>1/1000000000, "b"=>0 , "n" =>1 ),
                       ),

        "gb" => array(
                        "bit"   => array("a" =>8589934592 , "b"=>0 , "n" =>1 ),
                        "byte"  => array("a" =>1073741824 , "b"=>0 , "n" =>1 ),                                        
                        "kb"    => array("a" =>1048576 , "b"=>0 , "n" =>1 ),
                        "mb"    => array("a" =>1024, "b"=>0 , "n" =>1 ),
                        "tb"    => array("a" =>0.00097656, "b"=>0 , "n" =>1 ),
                        "gb"    => array("a" =>1, "b"=>0 , "n" =>1 ),
                        "pb"    => array("a" =>1/1000000, "b"=>0 , "n" =>1 ),
                       ),

        "tb" =>  array(
                        "bit"   => array("a" =>8796092251191.81, "b"=>0 , "n" =>1 ),
                        "byte"  => array("a" =>1099511531398.98, "b"=>0 , "n" =>1 ),                                        
                        "kb"    => array("a" =>1073741729.88, "b"=>0 , "n" =>1 ),
                        "mb"    => array("a" =>1048575.91, "b"=>0 , "n" =>1 ),
                        "gb"    => array("a" =>1024, "b"=>0 , "n" =>1 ),
                        "tb"    => array("a" =>1, "b"=>0 , "n" =>1 ),
                        "pb"    => array("a" =>0.00097656, "b"=>0 , "n" =>1 ),
                        ),

        "pb" =>  array(
                        "bit"   => array("a" =>9007198042096471, "b"=>0 , "n" =>1 ),
                        "byte"  => array("a" =>1125899755262058.9, "b"=>0 , "n" =>1 ),                                        
                        "kb"    => array("a" =>1099511479748.1, "b"=>0 , "n" =>1 ),
                        "mb"    => array("a" =>1073741679.44, "b"=>0 , "n" =>1 ),
                        "gb"    => array("a" =>1048575.86, "b"=>0 , "n" =>1 ),
                        "tb"    => array("a" =>1024, "b"=>0 , "n" =>1 ),
                        "pb"    => array("a" =>1, "b"=>0 , "n" =>1 ),
                        ),

        #------end digital information ------#

        #------Force Units ------#
        
        "dyne" => array(
                "dyne"   => array("a" => 1, "b" => 0, "n" => 1),
                "newton"   => array("a" => 0.00001, "b" => 0, "n" => 1),
            ),
        "newton" => array(
                "dyne"   => array("a" => 100000, "b" => 0, "n" => 1),
                "newton"   => array("a" => 1, "b" => 0, "n" => 1),
            ),
       

        #------END Force Units ------#

        #------ Mass Units ------#
        "kg" => array(
                    "grms"      => array("a" => 1000, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 1*1000000, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 100000, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 10000, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 1*1000000000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 10, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 2.20462, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 35.274, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 0.157473, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 0.001, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 1, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 0.01, "b" => 0, "n" => 1),
                ),

        "grms" => array(
                    "grms"      => array("a" => 1, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 1000, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 100, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 10, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 1000000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 0.01, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 0.00220462, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 0.035274, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 0.000157473, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 1/1000000, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 0.001, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 1/100000, "b" => 0, "n" => 1),
                ),

        "mg" => array(
                    "grms"      => array("a" => 0.001, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 1, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 0.1, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 0.01, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 1000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 1/100000, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 2.2046/1000000, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 3.5274/100000, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 1.5747/10000000, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 1/1000000000, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 1/1000000, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 1/100000000, "b" => 0, "n" => 1),
                ),

        "cg" => array(
                    "grms"      => array("a" => 0.01, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 10, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 1, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 0.1, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 10000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 0.0001, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 32.20462/100000, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 0.00035274, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 1.5747/1000000, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 1/100000000, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 1/100000, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 1.0/10000000, "b" => 0, "n" => 1),
                ),

        "dg" => array(
                    "grms"      => array("a" => 0.1, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 100, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 10, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 1, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 100000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 0.001, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 0.000220462, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 0.0035274, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 1.5747/100000, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 1/10000000, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 1/10000, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 1.0/1000000, "b" => 0, "n" => 1),
                ),
        "μg" => array(
                    "grms"      => array("a" => 1/1000000, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 0.001, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 0.0001, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 1/100000, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 1, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 1/100000000, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 2.20462/1000000000, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 3.5274/100000000, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 1.5747/10000000000, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 1/1000000000000, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 1/1000000000, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 1/100000000000, "b" => 0, "n" => 1),
                ),

        "hg" => array(
                    "grms"      => array("a" => 100, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 100000, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 10000, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 1000, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 1*100000000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 1, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 0.220462, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 3.5274, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 0.0157473, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 1/10000, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 0.1, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 0.001, "b" => 0, "n" => 1),
                ),

        "pounds" => array(
                    "grms"      => array("a" => 453.592, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 453592, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 45359.2, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 4535.92, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 4.536*100000000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 4.53592, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 1, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 16, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 0.0714286, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 0.000453592, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 0.453592, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 0.00453592, "b" => 0, "n" => 1),
                ),

        "ounce" => array(
                    "grms"      => array("a" => 28.3495, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 28349.5, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 2834.95, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 283.495, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 2.835*10000000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 0.283495, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 0.0625, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 1, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 0.00446429, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 2.835/100000, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 0.0283495, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 0.000283495, "b" => 0, "n" => 1),
                ),

        "stone" => array(
                    "grms"      => array("a" => 6350.29, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 6.35*1000000, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 635029, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 63502.9, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 6.35*1000000000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 63.5029, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 14, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 224, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 1, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 0.00635029, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 6.35029, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 0.0635029, "b" => 0, "n" => 1),
                ),

        "ton" => array(
                    "grms"      => array("a" => 1*1000000, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 1*1000000000, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 1*100000000, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 1*10000000, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 1*1000000000000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 10000, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 2204.62, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 35274, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 157.473, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 1, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 1000, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 9.07185, "b" => 0, "n" => 1),
                ),

        "quintal" => array(
                    "grms"      => array("a" => 100000, "b" => 0, "n" => 1),
                    "mg"        => array("a" => 1*100000000, "b" => 0, "n" => 1),
                    "cg"        => array("a" => 1*10000000, "b" => 0, "n" => 1),
                    "dg"        => array("a" => 1000000, "b" => 0, "n" => 1),
                    "μg"        => array("a" => 1*100000000000, "b" => 0, "n" => 1),
                    "hg"        => array("a" => 1000, "b" => 0, "n" => 1),
                    "pounds"    => array("a" => 220.462, "b" => 0, "n" => 1),
                    "ounce"     => array("a" => 3527.4, "b" => 0, "n" => 1),
                    "stone"     => array("a" => 15.7473, "b" => 0, "n" => 1),
                    "ton"       => array("a" => 0.1, "b" => 0, "n" => 1),
                    "kg"        => array("a" => 100, "b" => 0, "n" => 1),
                    "quintal"   => array("a" => 1, "b" => 0, "n" => 1),
                ),

        #------END Mass Units ------#

        #------Time Units ------#

        "millisecond" => array(
                        "millisecond"   => array("a" => 1, "b" => 0, "n" => 1),
                        "microsecond"   => array("a" => 1000, "b" => 0, "n" => 1),
                        "nanosecond"    => array("a" => 1*1000000, "b" => 0, "n" => 1),
                        "hour"          => array("a" => 2.7778/10000000, "b" => 0, "n" => 1),
                        "minute"        => array("a" => 1.66668/100000, "b" => 0, "n" => 1),
                        "second"        => array("a" => 0.001, "b" => 0, "n" => 1),
                        "day"           => array("a" => 1.1574/100000000, "b" => 0, "n" => 1),
                        "week"          => array("a" => 1.6534/1000000000, "b" => 0, "n" => 1),
                        "month"         => array("a" => 3.8052/10000000000, "b" => 0, "n" => 1),
                        "year"          => array("a" => 3.171/100000000000, "b" => 0, "n" => 1),
                        "decade"        => array("a" => 3.171/1000000000000, "b" => 0, "n" => 1),
                        "century"       => array("a" => 3.171/10000000000000, "b" => 0, "n" => 1),
                    ),

        "microsecond" => array(
                            "millisecond"   => array("a" => 0.001, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 1, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 1000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 2.7778/10000000000, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 1.6667/100000000, "b" => 0, "n" => 1),
                            "second"        => array("a" => 1/1000000, "b" => 0, "n" => 1),
                            "day"           => array("a" => 1.1574/100000000000, "b" => 0, "n" => 1),
                            "week"          => array("a" => 1.6534/1000000000000, "b" => 0, "n" => 1),
                            "month"         => array("a" => 3.8052/10000000000000, "b" => 0, "n" => 1),
                            "year"          => array("a" => 3.171/100000000000000, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 3.171/1000000000000000, "b" => 0, "n" => 1),
                            "century"       => array("a" => 3.171/10000000000000000, "b" => 0, "n" => 1),
                        ),

        "nanosecond" => array(
                            "millisecond"   => array("a" => 1/1000000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 0.001, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 1, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 2.7778/10000000000000, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 1.6667/100000000000, "b" => 0, "n" => 1),
                            "second"        => array("a" => 1/1000000000, "b" => 0, "n" => 1),
                            "day"           => array("a" => 1.1574/100000000000000, "b" => 0, "n" => 1),
                            "week"          => array("a" => 1.6534/1000000000000000, "b" => 0, "n" => 1),
                            "month"         => array("a" => 3.8052/10000000000000000, "b" => 0, "n" => 1),
                            "year"          => array("a" => 3.171/100000000000000000, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 3.171/1000000000000000000, "b" => 0, "n" => 1),
                            "century"       => array("a" => 3.171/10000000000000000000, "b" => 0, "n" => 1),
                        ),

        "hour" => array(
                            "millisecond"   => array("a" => 3.6*1000000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 3.6*1000000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 3.6*1000000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 1, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 60, "b" => 0, "n" => 1),
                            "second"        => array("a" => 3600, "b" => 0, "n" => 1),
                            "day"           => array("a" => 0.0416667, "b" => 0, "n" => 1),
                            "week"          => array("a" => 0.00595238, "b" => 0, "n" => 1),
                            "month"         => array("a" => 0.00136986, "b" => 0, "n" => 1),
                            "year"          => array("a" => 0.000114155, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 1.1416/100000, "b" => 0, "n" => 1),
                            "century"       => array("a" => 1.1416/1000000, "b" => 0, "n" => 1),
                        ),

        "minute" => array(
                            "millisecond"   => array("a" => 60000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 6*10000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 6*10000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 0.0166667, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 1, "b" => 0, "n" => 1),
                            "second"        => array("a" => 60, "b" => 0, "n" => 1),
                            "day"           => array("a" => 0.000694444, "b" => 0, "n" => 1),
                            "week"          => array("a" => 9.9206/100000, "b" => 0, "n" => 1),
                            "month"         => array("a" => 2.2831/100000, "b" => 0, "n" => 1),
                            "year"          => array("a" => 1.9026/1000000, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 1.9026/10000000, "b" => 0, "n" => 1),
                            "century"       => array("a" => 1.9026/100000000, "b" => 0, "n" => 1),
                        ),

        "second" => array(
                            "millisecond"   => array("a" => 1000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 1*1000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 1*1000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 0.000277778, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 0.0166667, "b" => 0, "n" => 1),
                            "second"        => array("a" => 1, "b" => 0, "n" => 1),
                            "day"           => array("a" => 1.1574/100000, "b" => 0, "n" => 1),
                            "week"          => array("a" => 1.6534/1000000, "b" => 0, "n" => 1),
                            "month"         => array("a" => 3.8052/10000000, "b" => 0, "n" => 1),
                            "year"          => array("a" => 3.171/100000000, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 3.171/1000000000, "b" => 0, "n" => 1),
                            "century"       => array("a" => 3.171/10000000000, "b" => 0, "n" => 1),
                        ),

        "day" => array(
                            "millisecond"   => array("a" => 8.64*10000000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 8.64*10000000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 8.64*10000000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 24, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 1440, "b" => 0, "n" => 1),
                            "second"        => array("a" => 86400, "b" => 0, "n" => 1),
                            "day"           => array("a" => 1, "b" => 0, "n" => 1),
                            "week"          => array("a" => 0.142857, "b" => 0, "n" => 1),
                            "month"         => array("a" => 0.0328767, "b" => 0, "n" => 1),
                            "year"          => array("a" => 0.00273973, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 0.000273973, "b" => 0, "n" => 1),
                            "century"       => array("a" => 2.7397/100000, "b" => 0, "n" => 1),
                        ),

        "week" => array(
                            "millisecond"   => array("a" => 6.048*100000000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 6.048*100000000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 6.048*100000000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 168, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 10080, "b" => 0, "n" => 1),
                            "second"        => array("a" => 604800, "b" => 0, "n" => 1),
                            "day"           => array("a" => 7, "b" => 0, "n" => 1),
                            "week"          => array("a" => 1, "b" => 0, "n" => 1),
                            "month"         => array("a" => 0.230137, "b" => 0, "n" => 1),
                            "year"          => array("a" => 0.0191781, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 0.00191781, "b" => 0, "n" => 1),
                            "century"       => array("a" => 0.000191781, "b" => 0, "n" => 1),
                        ),

        "month" => array(
                            "millisecond"   => array("a" => 2.628*1000000000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 2.628*1000000000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 2.628*1000000000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 730, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 43800, "b" => 0, "n" => 1),
                            "second"        => array("a" => 2.628*1000000, "b" => 0, "n" => 1),
                            "day"           => array("a" => 30, "b" => 0, "n" => 1),
                            "week"          => array("a" => 4.34524, "b" => 0, "n" => 1),
                            "month"         => array("a" => 0.230137, "b" => 0, "n" => 1),
                            "year"          => array("a" => 0.0191781, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 0.00191781, "b" => 0, "n" => 1),
                            "century"       => array("a" => 0.000191781, "b" => 0, "n" => 1),
                        ),

        "year" => array(
                            "millisecond"   => array("a" => 3.154*10000000000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 3.154*10000000000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 3.154*10000000000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 8760, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 525600, "b" => 0, "n" => 1),
                            "second"        => array("a" => 3.154*10000000, "b" => 0, "n" => 1),
                            "day"           => array("a" => 365, "b" => 0, "n" => 1),
                            "week"          => array("a" => 52.1429, "b" => 0, "n" => 1),
                            "month"         => array("a" => 12, "b" => 0, "n" => 1),
                            "year"          => array("a" => 1, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 0.1, "b" => 0, "n" => 1),
                            "century"       => array("a" => 0.01, "b" => 0, "n" => 1),
                        ),

        "decade" => array(
                            "millisecond"   => array("a" => 3.154*100000000000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 3.154*100000000000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 3.154*100000000000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 87600, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 5.256*1000000, "b" => 0, "n" => 1),
                            "second"        => array("a" => 3.154*100000000, "b" => 0, "n" => 1),
                            "day"           => array("a" => 3650, "b" => 0, "n" => 1),
                            "week"          => array("a" => 521.429, "b" => 0, "n" => 1),
                            "month"         => array("a" => 120, "b" => 0, "n" => 1),
                            "year"          => array("a" => 10, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 1, "b" => 0, "n" => 1),
                            "century"       => array("a" => 0.1, "b" => 0, "n" => 1),
                        ),

        "century" => array(
                            "millisecond"   => array("a" => 3.154*1000000000000, "b" => 0, "n" => 1),
                            "microsecond"   => array("a" => 3.154*1000000000000000, "b" => 0, "n" => 1),
                            "nanosecond"    => array("a" => 3.154*1000000000000000000, "b" => 0, "n" => 1),
                            "hour"          => array("a" => 876000, "b" => 0, "n" => 1),
                            "minute"        => array("a" => 5.256*10000000, "b" => 0, "n" => 1),
                            "second"        => array("a" => 3.154*1000000000, "b" => 0, "n" => 1),
                            "day"           => array("a" => 36500, "b" => 0, "n" => 1),
                            "week"          => array("a" => 5214.29, "b" => 0, "n" => 1),
                            "month"         => array("a" => 1200, "b" => 0, "n" => 1),
                            "year"          => array("a" => 100, "b" => 0, "n" => 1),
                            "decade"        => array("a" => 10, "b" => 0, "n" => 1),
                            "century"       => array("a" => 1, "b" => 0, "n" => 1),
                        ),

        #------END Time Units ------#
    
    );
    return $units;
}

?>
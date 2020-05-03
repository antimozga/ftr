<?php

class AutoModerator
{

    var $KEYWORDS;

    function __construct()
    {
        $keylist = file_get_contents(KEYLISTFILE);

        $this->KEYWORDS = array_filter(preg_split("/\r\n|\n|\r/", $keylist));
        // print_r($this->KEYWORDS);
    }

    function getWeight($str)
    {
        $weight = 0;
        $strup = mb_strtoupper($str);

        foreach ($this->KEYWORDS as $word) {
            $weight += mb_substr_count($strup, mb_strtoupper($word));
        }

        return $weight;
    }

    function str_word_count_utf8($str)
    {
        $a = preg_split('/\W+/u', $str, - 1, PREG_SPLIT_NO_EMPTY);
        return count($a);
    }

    public function moderated($str)
    {
        $w = $this->getWeight($str);
        $c = $this->str_word_count_utf8($str);
        $p = $w * 100 / $c;
        if ($p < 15 && $w <= 1) {
            return true;
        } else {
            // echo "$str $w $c $p \n";
            return false;
        }
    }
}

?>

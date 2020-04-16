<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Number_to_word
{
    private $word_array = [];
    private $thousand = [];
    private $val;
    private $currency0;
    private $currency_word;
    private $currency_symbol;
    private $currency1;
    private $ci;
    private $val_array;

    private $dec_value;

    private $dec_word;

    private $num_value;

    private $num_word;

    private $val_word;

    private $original_val;

    private $language;

    public function __construct($params = [])
    {
        $l = '';
        $this->ci = &get_instance();
        if (!empty($params['client_id']) && is_numeric($params['client_id'])) {
            $client_info = get_row('tbl_client', array('client_id' => $params['client_id']));
            if (!empty($client_info->language)) {
                if (file_exists(APPPATH . 'language/' . $client_info->language)) {
                    $l = $client_info->language;
                }
            }
            if (!empty($client_info->currency)) {
                $currency_code = $client_info->currency;
            }
        }
        $language = $l;
        if ($language == '') {
            $language = config_item('default_language');
            $currency_code = config_item('default_currency');
        }
        $currency_info = get_row('tbl_currencies', array('code' => $currency_code));
        $this->currency_word = $currency_info->name;
        $this->currency_symbol = $currency_info->code;
        unset($this->ci->lang->is_loaded['number_lang.php']);
        $this->ci->lang->load('number_lang', $language);
        $this->language = $language;
        array_push($this->thousand, '');
        array_push($this->thousand, lang('num_word_thousand') . ' ');
        array_push($this->thousand, lang('num_word_million') . ' ');
        array_push($this->thousand, lang('num_word_billion') . ' ');
        array_push($this->thousand, lang('num_word_trillion') . ' ');
        array_push($this->thousand, lang('num_word_zillion') . ' ');
        for ($i = 1; $i < 100; $i++) {
            $this->word_array[$i] = lang('num_word_' . $i);
        }
        for ($i = 100; $i <= 900; $i = $i + 100) {
            $this->word_array[$i] = lang('num_word_' . $i);
        }
    }

    public function convert($in_val = 0, $in_currency0 = '', $in_currency1 = true)
    {
        $this->original_val = $in_val;
        $this->val = $in_val;
        if (empty($in_currency0)) {
            $in_currency0 = $this->currency_symbol;
        }
        $this->currency0 = lang('num_word_' . mb_strtoupper($in_currency0, 'UTF-8'));

        if (strtolower($in_currency0) == 'inr') {
            $final_val = $this->convert_indian($in_val);
        } else {
            // Currency not found
            if (strpos($this->currency0, 'num_word_') !== false) {
                $this->currency0 = '';
            }
            if ($in_currency1 == false) {
                $this->currency1 = '';
            } else {
                $this->currency1 = lang('num_word_cents');
            }
            // remove commas from comma separated numbers
            $this->val = abs(floatval(str_replace(',', '', $this->val)));
            if ($this->val > 0) {
                // convert to number format
                $this->val = number_format($this->val, '2', ',', ',');
                // split to array of 3(s) digits and 2 digit
                $this->val_array = explode(',', $this->val);
                // separate decimal digit
                $this->dec_value = intval($this->val_array[count($this->val_array) - 1]);
                if ($this->dec_value > 0) {
                    $w_and = lang('number_word_and');
                    $w_and = ($w_and == ' ' ? '' : $w_and .= ' ');
                    // convert decimal part to word;
                    $this->dec_word = $w_and . '' . $this->word_array[$this->dec_value] . ' ' . $this->currency1;
                }
                // loop through all 3(s) digits in VAL array
                $t = 0;
                // initialize the number to word variable
                $this->num_word = '';

                for ($i = count($this->val_array) - 2; $i >= 0; $i--) {
                    // separate each element in VAL array to 1 and 2 digits
                    $this->num_value = intval($this->val_array[$i]);

                    // if VAL = 0 then no word
                    if ($this->num_value == 0) {
                        $this->num_word = ' ' . $this->num_word;
                    } // if 0 < VAL < 100 or 2 digits
                    elseif (strlen($this->num_value . '') <= 2) {
                        $this->num_word = $this->word_array[$this->num_value] . ' ' . $this->thousand[$t] . $this->num_word;
                        // add 'and' if not last element in VAL
                        if ($i == 1) {
                            $w_and = lang('number_word_and');
                            $w_and = ($w_and == ' ' ? '' : $w_and .= ' ');
                            $this->num_word = $w_and . '' . $this->num_word;
                        }
                    } // if VAL >= 100, set the hundred word
                    else {
                        @$this->num_word = $this->word_array[mb_substr($this->num_value, 0, 1) . '00'] . (intval(mb_substr($this->num_value, 1, 2)) > 0 ? (lang('number_word_and') != ' ' ? ' ' . lang('number_word_and') . ' ' : ' ') : '') . $this->word_array[intval(mb_substr($this->num_value, 1, 2))] . ' ' . $this->thousand[$t] . $this->num_word;
                    }
                    $t++;
                }
                // add currency to word
                if (!empty($this->num_word)) {
                    $this->num_word .= '' . $this->currency0;
                }
            }
            // join the number and decimal words
            $this->val_word = $this->num_word . ' ' . $this->dec_word;

            if (config_item('amount_to_words_lowercase') == 'Yes') {
                $final_val = trim(mb_strtolower($this->val_word, 'UTF-8'));
            } else {
                $final_val = trim($this->val_word);
            }
        }
        return $final_val . ' ' . $this->currency_word;
//        return hooks()->apply_filters('before_return_num_word', $final_val, [
//            'original_number' => $this->original_val,
//            'currency' => $in_currency0,
//            'language' => $this->language,
//        ]);
    }

    private function convert_indian($num)
    {
        $count = 0;
        global $ones, $tens, $triplets;
        $ones = [
            '',
            ' ' . lang('num_word_1'),
            ' ' . lang('num_word_2'),
            ' ' . lang('num_word_3'),
            ' ' . lang('num_word_4'),
            ' ' . lang('num_word_5'),
            ' ' . lang('num_word_6'),
            ' ' . lang('num_word_7'),
            ' ' . lang('num_word_8'),
            ' ' . lang('num_word_9'),
            ' ' . lang('num_word_10'),
            ' ' . lang('num_word_11'),
            ' ' . lang('num_word_12'),
            ' ' . lang('num_word_13'),
            ' ' . lang('num_word_14'),
            ' ' . lang('num_word_15'),
            ' ' . lang('num_word_16'),
            ' ' . lang('num_word_17'),
            ' ' . lang('num_word_18'),
            ' ' . lang('num_word_19'),
        ];
        $tens = [
            '',
            '',
            ' ' . lang('num_word_20'),
            ' ' . lang('num_word_30'),
            ' ' . lang('num_word_40'),
            ' ' . lang('num_word_50'),
            ' ' . lang('num_word_60'),
            ' ' . lang('num_word_70'),
            ' ' . lang('num_word_80'),
            ' ' . lang('num_word_90'),
        ];

        $triplets = [
            '',
            ' ' . lang('num_word_thousand'),
            ' ' . lang('num_word_million'),
            ' ' . lang('num_word_billion'),
            ' ' . lang('num_word_trillion'),
            ' Quadrillion',
            ' Quintillion',
            ' Sextillion',
            ' Septillion',
            ' Octillion',
            ' Nonillion',
        ];

        return $this->convert_number_indian($num);
    }

    /**
     * Function to dislay tens and ones
     */
    private function common_loop_indian($val, $str1 = '', $str2 = '')
    {
        global $ones, $tens;
        $string = '';
        if ($val == 0) {
            $string .= $ones[$val];
        } elseif ($val < 20) {
            $string .= $str1 . $ones[$val] . $str2;
        } else {
            $string .= $str1 . $tens[(int)($val / 10)] . $ones[$val % 10] . $str2;
        }

        return $string;
    }

    /**
     * returns the number as an anglicized string
     */
    private function convert_number_indian($num)
    {
        $num = (int)$num;    // make sure it's an integer

        if ($num < 0) {
            return 'negative' . $this->convert_tri_indian(-$num, 0);
        }

        if ($num == 0) {
            return 'Zero';
        }

        return $this->convert_tri_indian($num, 0);
    }

    /**
     * recursive fn, converts numbers to words
     */
    private function convert_tri_indian($num, $tri)
    {
        global $ones, $tens, $triplets, $count;
        $test = $num;
        $count++;
        // chunk the number, ...rxyy
        // init the output string
        $str = '';
        // to display hundred & digits
        if ($count == 1) {
            $r = (int)($num / 1000);
            $x = ($num / 100) % 10;
            $y = $num % 100;
            // do hundreds
            if ($x > 0) {
                $str = $ones[$x] . ' ' . (lang('num_word_hundred') === 'num_word_hundred' ? 'Hundred' : lang('num_word_hundred'));
                // do ones and tens
                $str .= $this->common_loop_indian($y, ' ' . lang('number_word_and') . ' ', '');
            } elseif ($r > 0) {
                // do ones and tens
                $str .= $this->common_loop_indian($y, ' ' . lang('number_word_and') . ' ', '');
            } else {
                // do ones and tens
                $str .= $this->common_loop_indian($y);
            }
        } // To display lakh and thousands
        elseif ($count == 2) {
            $r = (int)($num / 10000);
            $x = ($num / 100) % 100;
            $y = $num % 100;
            $str .= $this->common_loop_indian($x, '', (' ' . $this->get_lakh_text($x)));
            $str .= $this->common_loop_indian($y);
            if ($str != '') {
                $str .= $triplets[$tri];
            }
        } // to display till hundred crore
        elseif ($count == 3) {
            $r = (int)($num / 1000);
            $x = ($num / 100) % 10;
            $y = $num % 100;
            // do hundreds
            if ($x > 0) {
                $str = $ones[$x] . ' ' . (lang('num_word_hundred') === 'num_word_hundred' ? 'Hundred' : lang('num_word_hundred'));
                // do ones and tens
                $str .= $this->common_loop_indian($y, ' ' . lang('number_word_and') . ' ', ' Crore ');
            } elseif ($r > 0) {
                // do ones and tens
                $str .= $this->common_loop_indian($y, ' ' . lang('number_word_and') . ' ', ' Crore ');
            } else {
                // do ones and tens
                $str .= $this->common_loop_indian($y);
            }
        } else {
            $r = (int)($num / 1000);
        }
        // add triplet modifier only if there
        // is some output to be modified...
        // continue recursing?
        if ($r > 0) {
            return $this->convert_tri_indian($r, $tri + 1) . $str;
        }

        return $str;
    }

    private function get_lakh_text($x)
    {
        $key = $x <= 1 ? 'num_word_lakh' : 'num_word_lakhs';
        $text = lang($key);

        if ($text == $key) {
            return $x <= 1 ? 'Lakh' : 'Lakhs';
        }

        return $text;
    }
}

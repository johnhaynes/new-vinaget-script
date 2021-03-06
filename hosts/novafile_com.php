<?php

class dl_novafile_com extends Download
{

    public function CheckAcc($cookie)
    {
        $data = $this->lib->curl("https://novafile.com/?op=my_account", "lang=english;{$cookie}", "");
        if (stristr($data, 'Premium account expires:')) {
            $checkbw = $this->lib->curl("https://novafile.com/?op=my_account", "lang=english;{$cookie}", "");
            return array(true, "Until " . $this->lib->cut_str($data, 'Premium Account expires:', '<a href="') . "<br/> Traffic Available: " . $this->lib->cut_str($this->lib->cut_str($checkbw, '<td>Traffic Available:</td>', '</tr>'), '<td>', '</td>'));
        } elseif (stristr($data, 'FREE - member')) {
            return array(false, "accfree");
        } else {
            return array(false, "accinvalid");
        }

    }

    public function Login($user, $pass)
    {
        $data = $this->lib->curl("https://novafile.com/login", "lang=english", "login={$user}&password={$pass}&op=login&rand=&redirect=");
        return "lang=english;{$this->lib->GetCookies($data)}";
    }

    public function Leech($url)
    {
        $data = $this->lib->curl($url, $this->lib->cookie, "");
        if (stristr($data, '>File Not Found<')) {
            $this->error("dead", true, false, 2);
        } elseif (stristr($data, "different IP")) {
            $this->error("blockIP", true, false);
        } else {
            $post = $this->parseForm($this->lib->cut_str($data, '<form action="', '</form>'));
            $data = $this->lib->curl($url, $this->lib->cookie, $post);
            //if(stristr($data,'You have reached the download limit'))  $this->error("LimitAcc", true, false);
            if (preg_match('@You have reached the download limit: (\d+) (T|G|M|K)B@i', $data, $limit)) {
                $this->error($limit[0], true, false);
            } else {
                return trim($this->lib->cut_str($data, '<p><a href="', '" class="btn btn-green'));
            }

        }
        return false;
    }

}

/*
 * Open Source Project
 * New Vinaget by LTT
 * Version: 3.3 LTS
 * Novafile.com Download Plugin
 * Date: 15.11.2017
 */

<?php
require_once 'logic/func.php';
$func = new func();
class logic
{
    public function new_message($send, $message){
        switch ($message) {
            default:
                //$send->message($message);
        }
    }
    public function new_reply($send, $message, $reply_message){
        switch ($reply_message) {
            case 'Reply this message. Write: Encryptkey-key':
                $send->message("Reply this message. Write string to encode. Your key - ".trim(str_replace('Encryptkey-','',$message)));
                break;
            case 'Reply this message. Write: Decryptkey-key':
                $send->message("Reply this message. Write string to decode. Your key - ".trim(str_replace('Decryptkey-','',$message)));
                break;
        }
        if(stripos($reply_message, "Reply this message. Write string to encode. Your key - ") !== false){
            define('ENCRYPTION_KEY', trim(str_replace('Reply this message. Write string to encode. Your key - ','',$reply_message)));
            // Encrypt
            $plaintext = $message;
            $ivlen = openssl_cipher_iv_length($cipher="AES-256-CTR");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($plaintext, $cipher, ENCRYPTION_KEY, $options=OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, $as_binary=true);
            $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
            $send->message($ciphertext);
        }else
        if(stripos($reply_message, "Reply this message. Write string to decode. Your key - ") !== false){
            define('ENCRYPTION_KEY', trim(str_replace('Reply this message. Write string to decode. Your key - ','',$reply_message)));
            // Decrypt
            $c = base64_decode($message);
            $ivlen = openssl_cipher_iv_length($cipher="AES-256-CTR");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len=32);
            $ciphertext_raw = substr($c, $ivlen+$sha2len);
            $plaintext = openssl_decrypt($ciphertext_raw, $cipher, ENCRYPTION_KEY, $options=OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, $as_binary=true);
            if (hash_equals($hmac, $calcmac))
            {
                $send->message($plaintext);
            }else{
                $send->message("Decrypt key is not right!!!");
            }
        }
    }
    public function new_command($send, $message){
        global $func;
        switch ($message) {
            case '/start':
                $send->message("Hi! Do anything");
                break;
            case '/help':
                $send->message("THIS IS HELP");
                break;
            case '/weatherperm':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Now','callback_data' => '/weatherperm_now'],
                            ['text' => 'Week','callback_data' => '/weatherperm_week']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard("weather Perm:",$encodedKeyboard);
                break;
            case '/weatherperm_back':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Now','callback_data' => '/weatherperm_now'],
                            ['text' => 'Week','callback_data' => '/weatherperm_week']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->editMessageText("weather Perm:",$encodedKeyboard);
                break;
            case '/weatherperm_now':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/weatherperm_back']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $appid = '57c5def75a9d9dbc18fb0363276b807a'; //USE YOUR appid, it's free. JUST GO TO openweathermap.org
                $log1 = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=Perm&appid={$appid}&units=metric");
                $log = json_decode($log1, true); 
                $log3 = $log['weather'][0]['description'];
                $log4 = $log['main']['temp'];
                $log5 = $log['main']['feels_like'];
                $log2 = "weather perm:\nIt's {$log3}, {$log4},\nbut feels like {$log5}";
                $send->editMessageText($log2, $encodedKeyboard);
                break;
            case '/weatherperm_week':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/weatherperm_back']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $appid = '57c5def75a9d9dbc18fb0363276b807a'; //USE YOUR appid, it's free. JUST GO TO openweathermap.org
                $log1 = file_get_contents("https://api.openweathermap.org/data/2.5/onecall?lat=58.02&lon=56.29&exclude=dayly&appid={$appid}&units=metric");
                $log2 = json_decode($log1, true); //forecast
                $date1 = date('j M, l',$log2['daily'][0]['dt']);
                $date2 = date('j M, l',$log2['daily'][1]['dt']);
                $date3 = date('j M, l',$log2['daily'][2]['dt']);
                $date4 = date('j M, l',$log2['daily'][3]['dt']);
                $date5 = date('j M, l',$log2['daily'][4]['dt']);
                $date6 = date('j M, l',$log2['daily'][5]['dt']);
                $date7 = date('j M, l',$log2['daily'][6]['dt']);
                $date8 = date('j M, l',$log2['daily'][7]['dt']);
                $log3 = $log2['daily'][0]['temp']['day'];
                $log4 = $log2['daily'][0]['temp']['night'];
                $log5 = $log2['daily'][1]['temp']['day'];
                $log6 = $log2['daily'][1]['temp']['night'];
                $log7 = $log2['daily'][2]['temp']['day'];
                $log8 = $log2['daily'][2]['temp']['night'];
                $log9 = $log2['daily'][3]['temp']['day'];
                $log10 = $log2['daily'][3]['temp']['night'];
                $log11 = $log2['daily'][4]['temp']['day'];
                $log12 = $log2['daily'][4]['temp']['night'];
                $log13 = $log2['daily'][5]['temp']['day'];
                $log14 = $log2['daily'][5]['temp']['night'];
                $log15 = $log2['daily'][6]['temp']['day'];
                $log16 = $log2['daily'][6]['temp']['night'];
                $log17 = $log2['daily'][7]['temp']['day'];
                $log18 = $log2['daily'][7]['temp']['night'];
                $log19 = "weather perm:\n{$date1}: {$log3}, night: {$log4};\n{$date2}: {$log4}, night: {$log6};\n{$date3}: {$log7}, night: {$log8};\n{$date4}: {$log9}, night: {$log10};\n{$date5}: {$log11}, night: {$log12};\n{$date6}: {$log13}, night: {$log14};\n{$date7}: {$log15}, night: {$log16};\n{$date8}: {$log17}, night: {$log18};";
                //$send->message($log19);
                $send->editMessageText($log19, $encodedKeyboard);
                break;
            case '/reboot_server':
                $send->message("NO REPEAT THIS COMMAND, server is rebooting!");
                $func->reboot();
                break;
            case '/vip':
                if($send->user_id == 505103225){
                     $keyboard = [
                    'inline_keyboard' => [
                            [
                                ['text' => 'Reboot','callback_data' => '/reboot_server'],
                                ['text' => 'Info','callback_data' => '/info_server']
                            ]
                        ]
                    ];
                    $encodedKeyboard = json_encode($keyboard);
                    $send->keyboard("Functions:",$encodedKeyboard);
                    break;
                }else{
                    $send->message("You are not Vitasha, sry...");
                    break;   
                }
            case '/info_server':
                $send->message($func->info());
                break;
            case '/coin':
                $send->message("Монетка решила показать сторону ".$func->coin());
                break;
            case '/encrypt':
                $send->message("Reply this message. Write: Encryptkey-key");
                break;
            case '/decrypt':
                $send->message("Reply this message. Write: Decryptkey-key");
                break;
            case '/random':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '1-10','callback_data' => '/random_1-10'],
                            ['text' => '1-100','callback_data' => '/random_1-100'],
                            ['text' => '1-1000','callback_data' => '/random_1-1000'],
                            ['text' => '1-1000000','callback_data' => '/random_1-1000000']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard("Random.org:",$encodedKeyboard);
                break; //$func->coin()
            case '/random_1-10':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/random_back']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->editMessageText($func->random(1,10), $encodedKeyboard);
                break;
            case '/random_1-100':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/random_back']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->editMessageText($func->random(1,100), $encodedKeyboard);
                break;
            case '/random_1-1000':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/random_back']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->editMessageText($func->random(1,1000), $encodedKeyboard);
                break;
            case '/random_1-1000000':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/random_back']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->editMessageText($func->random(1,1000000), $encodedKeyboard);
                break;
            case '/random_back':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '1-10','callback_data' => '/random_1-10'],
                            ['text' => '1-100','callback_data' => '/random_1-100'],
                            ['text' => '1-1000','callback_data' => '/random_1-1000'],
                            ['text' => '1-1000000','callback_data' => '/random_1-1000000']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->editMessageText("Random.org:",$encodedKeyboard);
                break;
            case '/quotes':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Next','callback_data' => '/quotes_new']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard($func->quotes(),$encodedKeyboard);
                break;
            case '/quotes_new':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Next','callback_data' => '/quotes_new']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->deleteKeyboard();
                $send->keyboard($func->quotes(), $encodedKeyboard);
                break;
            case '/compatibility':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Count!','callback_data' => '/compatibility_count']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard("Write new message or imagine about anything, then click to Count!",$encodedKeyboard);
                break;
            case '/compatibility_count':
                $send->message("Совместимость того, о чём ты думаешь - ".$func->random(1,100)."%");
                break;
            case '/music':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'No words','callback_data' => '/music_nowords'],
                            ['text' => 'Alone','callback_data' => '/music_alone'],
                            ['text' => 'Happy','callback_data' => '/music_happy']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard("We recommend",$encodedKeyboard);
                //$send->audio("CQACAgQAAxkDAAII3F7xyOXX_cBOmGo2TzNHQmerNZmFAAIfAgAC_yWUU4m6jpZBcfi9GgQ");
                break;
            case '/music_nowords':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'No words','callback_data' => '/music_nowords'],
                            ['text' => 'Alone','callback_data' => '/music_alone'],
                            ['text' => 'Happy','callback_data' => '/music_happy']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard("We recommend",$encodedKeyboard);
                break;
            case '/music_alone':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'No words','callback_data' => '/music_nowords'],
                            ['text' => 'Alone','callback_data' => '/music_alone'],
                            ['text' => 'Happy','callback_data' => '/music_happy']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard("We recommend",$encodedKeyboard);
                break;
            case '/music_happy':
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'No words','callback_data' => '/music_nowords'],
                            ['text' => 'Alone','callback_data' => '/music_alone'],
                            ['text' => 'Happy','callback_data' => '/music_happy']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard("We recommend",$encodedKeyboard);
                break;
            /*
            case '/history': 
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Start','callback_data' => '/history_start']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->keyboard("История выдумана, все совпадения случайны, надеюсь вам всё понравится!!!",$encodedKeyboard);
                break;
            case '/history_start': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '1','callback_data' => '/history_2_1'],
                            ['text' => '2','callback_data' => '/history_2_2'],
                            ['text' => 'Back','callback_data' => '/history_back']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text1 = "Шёл июнь 2020 года. Какой-то там день лета, наверно, мало кто следит за этим. Уже давно наступил день, но ты только сейчас удосужился проснуться.
                \n-Какое прекрасное утро! - говоришь ты и сразу решаешь...
                \n 
                \n 1.Идти кушать
                \n 2.Проверить телефон
                ";
                $send->editMessageText($text1, $encodedKeyboard);
                break;
            case '/history_2_1': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '1','callback_data' => '/history_3_1'],
                            ['text' => '2','callback_data' => '/history_3_2'],
                            ['text' => 'Back','callback_data' => '/history_start']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_1 = "...говоришь ты и сразу решаешь идти кушать. Как странно, дома никого нет... Интересно где все? 
                \n - АУ! Вы где?
                \n Странно... Никто не отвечает... Поем, выйду во двор, быть может они ушли гулять...
                \n Выйдя на улицу, ты никого не увидел.
                \n А где все? Какого чёрта на улице пусто? Где все дети? 
                \n Тебя очень взбесила такая огромная куча вопросов, и ты решаешь просто пойти погулять.
                \n Идя по проспекту ты увидел кого-то в далеке.
                \n
                \n 1. Это единственный человек! Надо догнать его...
                \n 2. Ну вот там то есть люди, а где все мои?
                ";
                $send->editMessageText($text2_1, $encodedKeyboard);
                break;
            case '/history_2_2': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '1','callback_data' => '/history_3_3'],
                            ['text' => '2','callback_data' => '/history_3_4'],
                            ['text' => 'Back','callback_data' => '/history_start']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "...говоришь ты и сразу решаешь проверить телефон. Удивительно... Тебе написала Элина...
                \n - Привет
                \n - Что делаешь?
                \n Интересно, ей опять что-то нужно, или же можно расчитывать на что-то новенькое? Отвечу, может, правда будет что-то интересное.
                \n
                \n 1. - Привет, только проснулся, а ты?
                \n 2. - Ну привет, что тебе надо?
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
             case '/history_3_1': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '1','callback_data' => '/history_4_1'],
                            ['text' => '2','callback_data' => '/history_4_2'],
                            ['text' => 'Back','callback_data' => '/history_2_1']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_1 = "В надеждах встретить кого-то знакомого, ты побежал.
                \n - Не верю своим глазам... Это Эля?
                \n - ЭЛЯЯЯЯ! ЭТО ТЫ? ОСТАНОВИСЬ...
                \n Это была действительно она, обернувшись, она сказала:
                \n - Привет! Что ты тут делаешь? Где все?
                \n Ты очень испугался, но понял, вы остались вдвоём, не понятно, куда же исчезли все остальные...
                \n
                \n 1. - Давай прогуляемся по городу, быть может кого-то и найдём... 
                \n 2. - Что будем делать? У меня нет идей... 
                ";
                $send->editMessageText($text2_1, $encodedKeyboard);
                break;
            case '/history_3_2': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_2_1']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "Ты продолжил идти в другую сторону, так и не встретив более никого из людей.
                \nВскоре ты сошёл с ума от одиночества и от страха, ведь ты остался совсем один и совсем не знал, что же делать дальше.
                \nКонец этой ветки рассказа, попробуй что-то изменить!
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_3_3': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '1','callback_data' => '/history_4_3'],
                            ['text' => '2','callback_data' => '/history_4_4'],
                            ['text' => 'Back','callback_data' => '/history_2_2']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_1 = " - Привет, только проснулся, а ты?
                \n - Круто! Го гулять, у меня нет тёти дома, значит, никто не спалит.
                \n - У меня тоже никого нет, го, я буду у твоего дома через 20 минут.
                \n Выйдя на улицу, ты никого не увидел. А где все? Какого чёрта на улице пусто? Где все дети? Тебя очень взбесила такая огромная куча вопросов, и ты решаешь просто идти за Элькой.
                \n Подойдя к её дому тебе не пришлось долго ждать... Она вышла почти сразу же. 
                \n - Привет! Что ты тут делаешь? Где все?
                \n Ты очень испугался, но понял, вы остались вдвоём, не понятно, куда же исчезли все остальные...
                \n
                \n 1. - Давай прогуляемся по городу, быть может кого-то и найдём... 
                \n 2. - Что будем делать? У меня нет идей... 
                ";
                $send->editMessageText($text2_1, $encodedKeyboard);
                break;
            case '/history_3_4': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_2_2']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = " - Ну привет, что тебе надо?
                \n - Капец ты злой, лети в ЧС!
                \nВскоре ты сошёл с ума от одиночества и от страха, ведь ты остался совсем один и совсем не знал, что же делать дальше.
                \nКонец этой ветки рассказа, попробуй что-то изменить!
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_4_1': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_5_1'],
                            ['text' => 'Back','callback_data' => '/history_5_2'],
                            ['text' => 'Back','callback_data' => '/history_3_1']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = " - Давай прогуляемся по городу, быть может кого-то и найдём... 
                \n У тебя дрожжал голос, ты явно не знал, что же делать, и почему всё именно так.
                \n Вы пошли по аллее Паркового. Вы были настолько сконфуженны происходящим, что даже не нашли тему для разговора. В какой-то момент Эля всё же сказала:
                \n - Так странно, ни машин, ни людей. 
                \n - Знаешь... У меня есть одна идейка, как жто можно использовать... Смотри, вон там стоит мотоцикл, покатаемся?
                \n Эля сначала не соглашалась, но вскоре ты смог её уговорить.
                \n И вы решили поехать...
                \n 
                \n1. На хату к Эле.
                \n2. На Каму.
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_4_2': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_5_3'],
                            ['text' => 'Back','callback_data' => '/history_5_4'],
                            ['text' => 'Back','callback_data' => '/history_3_1']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "- Что будем делать? У меня нет идей... 
                \n - Давай просто погуляем, быть может мы кого-то встретим...
                \n У Эли дрожжал голос, она явно не знала, что же делать, и почему всё именно так.
                \n Вы пошли по аллее Паркового. Вы были настолько сконфуженны происходящим, что даже не нашли тему для разговора. В какой-то момент Эля всё же сказала:
                \n - Так странно, не машин, не людей. 
                \n - Всё как в сказке, неужели, и вправду, можно делать всё, что угодно?..
                \n - Если так, то пошли в магазин... - В глазах Эли явно появился азарт, ведь, если магазины пустые, в них можно и поесть, и взять алкоголь, и многое-многое другое.
                \n Ближайщим магазином оказалась семья, зайдя, вы всё-же были очень удивлены отсутсвием людей. 
                \n - За алкашкой! - дружно промолвили вы и пошли в соотвутсвующий раздел.
                \n Взяв пакетик с кассы и положив в него пару бутылок Эссы и Гаража, вы решили пойти за энергетиком. Взяв и его, вы, улыбаясь, ...
                \n 
                \n1. Решили продолжить грабить магаз, потому что есть вы тоже хотели.
                \n2. Решили пойти любимую площадку. 
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_4_3': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_5_5'],
                            ['text' => 'Back','callback_data' => '/history_5_6'],
                            ['text' => 'Back','callback_data' => '/history_3_3']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = " 
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_4_4': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_5_7'],
                            ['text' => 'Back','callback_data' => '/history_5_8'],
                            ['text' => 'Back','callback_data' => '/history_3_3']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "- Что будем делать? У меня нет идей... 
                \n - Давай просто погуляем, быть может мы кого-то встретим...
                \n У Эли дрожжал голос, она явно не знала, что же делать, и почему всё именно так.
                \n Вы пошли по аллее Паркового. Вы были настолько сконфуженны происходящим, что даже не нашли тему для разговора. В какой-то момент Эля всё же сказала:
                \n - Так странно, не машин, не людей. 
                \n - Всё как в сказке, неужели, и вправду, можно делать всё, что угодно?..
                \n - Если так, то пошли в магазин... - В глазах Эли явно появился азарт, ведь, если магазины пустые, в них можно и поесть, и взять алкоголь, и многое-многое другое.
                \n Ближайщим магазином оказалась семья, зайдя, вы всё-же были очень удивлены отсутсвием людей. 
                \n - За алкашкой! - дружно промолвили вы и пошли в соотвутсвующий раздел.
                \n Взяв пакетик с кассы и положив в него пару бутылок Эссы и Гаража, вы решили пойти за энергетиком. Взяв и его, вы, улыбаясь, ...
                \n 
                \n1. Решили продолжить грабить магаз, потому что есть вы тоже хотели.
                \n2. Решили пойти любимую площадку. 
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_5_1': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_4_1']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "На хату к Эле.
                \nТебе очень нравился этот вариант, ведь ты никогда не был у неё дома.
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_5_2': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_4_1']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "На Каму.
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_5_3': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_4_2']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "Решили продолжить грабить магаз, потому что есть вы тоже хотели.
                
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_5_4': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_4_2']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "Решили пойти любимую площадку. 
                
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
             case '/history_5_5': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_4_3']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "продолжение завтра history_5_5
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_5_6': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_4_3']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "продолжение завтра history_5_6
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_5_7': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_4_4']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "Решили продолжить грабить мага7з, потому что есть вы тоже хотели.
                
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_5_8': //
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Back','callback_data' => '/history_4_4']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $text2_2 = "Решили пойти любимую площадку. 8
                
                ";
                $send->editMessageText($text2_2, $encodedKeyboard);
                break;
            case '/history_back': //all
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Start','callback_data' => '/history_start']
                        ]
                    ]
                ];
                $encodedKeyboard = json_encode($keyboard);
                $send->editMessageText("История выдумана, все совпадения случайны, надеюсь вам всё понравится!!!",$encodedKeyboard);
                break;
                */
            default:
                $send->message("What???");
                break;
        }
    }
}
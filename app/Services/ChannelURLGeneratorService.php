<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Support\Str;
use Mormat\FormulaInterpreter\Functions\CallableFunction;

class ChannelURLGeneratorService
{
    /**
     *
     * This method will compile the string interpretation given to the Query params and generate the URL for that
     *
     * @param Product $channel
     * @param User $loggedUser
     * @return mixed|string
     */
    public function createURL(Channel $channel, User $loggedUser)
    {
        $url = $channel->main_url;

        if (!empty($channel->url_params) && is_array($channel->url_params)) {

            $compiler = new \Mormat\FormulaInterpreter\Compiler();

            $count = 0;

            foreach ($channel->url_params as $param) {

                preg_match('~\{([^}]*)\}~', $param, $match);

                if (empty($match)) {
                    continue;
                }

                $interpritString = $match[1];
                $queryVar = Str::replace($match[0], '', $param);

                $url .= (($count > 0) ? '&' : '?') . $queryVar;

                //=======================================================================
                // telephone_1 case
                // this will have two options
                //  1) exclude country code
                //  2) include country code
                //=======================================================================


                // check if param contains `telephone_1`
                if (Str::contains($interpritString, 'telephone_1')) {

                    // check if param contains `no_country_code`
                    if (Str::contains($interpritString, 'no_country_code')) {

                        $compiler->registerCustomFunction(new CallableFunction('no_country_code', function ($val) {
                            return Str::replace(['-', '_', '#'], '', preg_replace('/^010/', '', $val));
                        }));
                    }

                    $executable = $compiler->compile($interpritString);

                    $url .= $executable->run([
                        'telephone_1' => $loggedUser->contacts()->first()?->telephone_1
                    ]);
                }


                //=======================================================================
                //code case
                //=======================================================================

                // check if param contains `code`
                if (Str::contains($interpritString, 'user_code')) {

                    $executable = $compiler->compile($interpritString);

                    $url .= $executable->run([
                        'user_code' => $loggedUser->code
                    ]);

                }


                $count++;
            }

        }

        return $url;

    }


}

<?php

namespace App\Domain\Autoservice\Actions;

use Illuminate\Support\Facades\Log;

class GetAutoserviceAction
{
    private const AUTO_SERVICE_URL = 'https://euroauto.ru/autoservice';

    public function execute(): void
    {
        set_time_limit(1500000);
        $curl = curl_init();
        $works = json_decode(file_get_contents(base_path('works.json')), true);
        $userAgentArray = preg_split('/\n/', file_get_contents(base_path('userAgentsList.txt')));


        foreach ($works['works'] as $work) {
            try {
                $info = [];
                $emptyHour = [];
                $path = str_replace('/', '-', $work['part_name']);
                logger()->info("Работа - {$work['part_name']}");
                $workUrn = $work['urn'];
                if (blank($workUrn)) {
                    continue;
                }
                $userAgent = array_rand(array_flip($userAgentArray));
                curl_setopt_array($curl, [
                    CURLOPT_URL => self::AUTO_SERVICE_URL . "/{$workUrn}",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json, text/javascript, */*; q=0.01',
                        'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                        'Referer: https://euroauto.ru/autoservice/',
                        'Sec-Fetch-Dest: empty',
                        'Sec-Fetch-Mode: cors',
                        'Sec-Fetch-Site: same-origin',
                        "User-Agent: {$userAgent}",
                        'X-Requested-With: XMLHttpRequest',
                        'sec-ch-ua: "Chromium";v="110", "Not A(Brand";v="24", "Google Chrome";v="110"',
                        'sec-ch-ua-mobile: ?0',
                        'sec-ch-ua-platform: "Linux"'
                    ],
                ]);
                $response = json_decode(curl_exec($curl));

                if (!$response) {
                    continue;
                }

                $firms = $response->data?->collection?->firms;

                if (!$firms) {
                    continue;
                }

                foreach ($firms as $firm) {
                    Log::info('Машина - ', [$firm->name]);
                    $firmUrn = $firm->urn ?? null;
                    if (blank($firmUrn)) {
                        continue;
                    }

                    curl_setopt_array($curl, [
                        CURLOPT_URL => self::AUTO_SERVICE_URL . "/{$workUrn}" . "/{$firmUrn}",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => [
                            'Accept: application/json, text/javascript, */*; q=0.01',
                            'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                            'Referer: https://euroauto.ru/autoservice/',
                            'Sec-Fetch-Dest: empty',
                            'Sec-Fetch-Mode: cors',
                            'Sec-Fetch-Site: same-origin',
                            "User-Agent: {$userAgent}",
                            'X-Requested-With: XMLHttpRequest',
                            'sec-ch-ua: "Chromium";v="110", "Not A(Brand";v="24", "Google Chrome";v="110"',
                            'sec-ch-ua-mobile: ?0',
                            'sec-ch-ua-platform: "Linux"'
                        ],
                    ]);
                    $response = json_decode(curl_exec($curl));

                    if (!$response) {
                        continue;
                    }

                    $models = $response->data?->collection?->models;

                    if (!$models) {
                        continue;
                    }

                    foreach ($models as $model) {
                        $modelGroupUrn = $model->group_urn ?? null;
                        if (blank($modelGroupUrn)) {
                            continue;
                        }
                        curl_setopt_array($curl, [
                            CURLOPT_URL => self::AUTO_SERVICE_URL . "/{$workUrn}" . "/{$firmUrn}" . "/{$modelGroupUrn}",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => [
                                'Accept: application/json, text/javascript, */*; q=0.01',
                                'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                                'Referer: https://euroauto.ru/autoservice/',
                                'Sec-Fetch-Dest: empty',
                                'Sec-Fetch-Mode: cors',
                                'Sec-Fetch-Site: same-origin',
                                "User-Agent: {$userAgent}",
                                'X-Requested-With: XMLHttpRequest',
                                'sec-ch-ua: "Chromium";v="110", "Not A(Brand";v="24", "Google Chrome";v="110"',
                                'sec-ch-ua-mobile: ?0',
                                'sec-ch-ua-platform: "Linux"'
                            ],
                        ]);

                        $response = json_decode(curl_exec($curl));

                        if (!$response) {
                            continue;
                        }

                        $modifications = $response->data?->collection?->modifications;

                        if (!$modifications) {
                            continue;
                        }

                        foreach ($modifications as $modification) {

                            $modificationUrn = $modification->urn ?? null;
                            if (blank($modificationUrn)) {
                                continue;
                            }

                            curl_setopt_array($curl, [
                                CURLOPT_URL => self::AUTO_SERVICE_URL . "/{$workUrn}" . "/{$firmUrn}" . "/{$modelGroupUrn}" . "/{$modificationUrn}",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                                CURLOPT_HTTPHEADER => [
                                    'Accept: application/json, text/javascript, */*; q=0.01',
                                    'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                                    'Referer: https://euroauto.ru/autoservice/',
                                    'Sec-Fetch-Dest: empty',
                                    'Sec-Fetch-Mode: cors',
                                    'Sec-Fetch-Site: same-origin',
                                    "User-Agent: {$userAgent}",
                                    'X-Requested-With: XMLHttpRequest',
                                    'sec-ch-ua: "Chromium";v="110", "Not A(Brand";v="24", "Google Chrome";v="110"',
                                    'sec-ch-ua-mobile: ?0',
                                    'sec-ch-ua-platform: "Linux"'
                                ],
                            ]);
                            $response = json_decode(curl_exec($curl));

                            if (!$response) {
                                continue;
                            }

                            $price = $response->data?->price ?? null;

                            if (blank($price)) {
                                $emptyHour[] = [
                                    [
                                        'work' => [
                                            'name' => $work['part_name'] ?? null,
                                            'group' => $work['group_name'] ?? null,
                                        ],
                                        'car' => [
                                            'name' => $firm->name ?? null,
                                            'model' => $model->group_name ?? null,
                                            'modification' => $modification->name_year ?? null,
                                        ],
                                        'url' => self::AUTO_SERVICE_URL . "/{$workUrn}" . "/{$firmUrn}" . "/{$modelGroupUrn}" . "/{$modificationUrn}",
                                    ]
                                ];

                                continue;
                            }

                            $info[] = [
                                'work' => [
                                    'name' => $work['part_name'] ?? null,
                                    'group' => $work['group_name'] ?? null,
                                ],
                                'car' => [
                                    'name' => $firm->name ?? null,
                                    'model' => $model->group_name ?? null,
                                    'modification' => $modification->name_year ?? null,
                                ],
                                'price' => [
                                    'price_min' => $price->min_price ?? null,
                                    'price_max' => $price->max_price ?? null,
                                ],
                                'time_in_hour' => [
                                    'min_hour' => $price->min_hour ?? null,
                                    'max_hour' => $price->max_hour ?? null,
                                ]
                            ];
                        }
                    }
                }

                $json = json_encode($info);
                file_put_contents(base_path("works-json/{$path}.json"), $json);

                if (!blank($emptyHour)) {
                    $emptyJson = json_encode($emptyHour);
                    file_put_contents(base_path("works-json/empty/{$path}.json"), $emptyJson);
                }
            } catch (\Exception $e) {
                $json = json_encode($info);
                file_put_contents(base_path("works-json/{$path}.json"), $json);

                if (!blank($emptyHour)) {
                    $emptyJson = json_encode($emptyHour);
                    file_put_contents(base_path("works-json/empty/{$path}.json"), $emptyJson);
                }
                logger()->error('Произошла ошибка', [$e->getMessage(), $e->getTrace()]);
            }
        }
        curl_close($curl);
    }
}

<?php

class Business_day_calculator
{

    const DIA_DOMINGO = 0;
    const DIA_SEGUNDA = 1;
    const DIA_TERCA = 2;
    const DIA_QUARTA = 3;
    const DIA_QUINTA = 4;
    const DIA_SEXTA = 5;
    const DIA_SABADO = 6;

    private $holidays = [];

    private function isHoliday($date)
    {
        $ano = intval(date('Y'));

        $pascoa     = easter_date($ano);
        $dia_pascoa = date('j', $pascoa);
        $mes_pascoa = date('n', $pascoa);
        $ano_pascoa = date('Y', $pascoa);

        $feriados = array(
            // Datas Fixas dos feriados brasileiros
            'Ano Novo'                 => ['time' => mktime(0, 0, 0, 1,  1,   $ano), 'e_carnaval' => false], // Confraternização Universal - Lei nº 662, de 06/04/49
            'Tiradentes'               => ['time' => mktime(0, 0, 0, 4,  21,  $ano), 'e_carnaval' => false], // Tiradentes - Lei nº 662, de 06/04/49
            'Dia do Trabalhador'       => ['time' => mktime(0, 0, 0, 5,  1,   $ano), 'e_carnaval' => false], // Dia do Trabalhador - Lei nº 662, de 06/04/49
            'Independência do Brasil'  => ['time' => mktime(0, 0, 0, 9,  7,   $ano), 'e_carnaval' => false], // Dia da Independência - Lei nº 662, de 06/04/49
            'Nossa Senhora Aparecida'  => ['time' => mktime(0, 0, 0, 10,  12, $ano), 'e_carnaval' => false], // N. S. Aparecida - Lei nº 6802, de 30/06/80
            'Finados'                  => ['time' => mktime(0, 0, 0, 11,  2,  $ano), 'e_carnaval' => false], // Todos os santos - Lei nº 662, de 06/04/49
            'Proclamação da República' => ['time' => mktime(0, 0, 0, 11, 15,  $ano), 'e_carnaval' => false], // Proclamação da republica - Lei nº 662, de 06/04/49
            'Natal'                    => ['time' => mktime(0, 0, 0, 12, 25,  $ano), 'e_carnaval' => false], // Natal - Lei nº 662, de 06/04/49
            '1 Dia de Carnaval'        => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48,  $ano_pascoa), 'e_carnaval' => 3], //2ºferia Carnaval
            '2 Dia de Carnaval'        => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47,  $ano_pascoa), 'e_carnaval' => 2], //3ºferia Carnaval
            'Sexta-feira da Paixão'    => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2,  $ano_pascoa), 'e_carnaval' => false], //6ºfeira Santa
            'Páscoa'                   => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa,  $ano_pascoa), 'e_carnaval' => false], //Pascoa
            'Corpus Christi'           => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60,  $ano_pascoa), 'e_carnaval' => false], //Corpus Cirist
        );

        foreach ($feriados as  $feriado) {
            $this->holidays[] = date('m-d', $feriado['time']);
        }

        $formattedDate = $date->format('m-d');

        return in_array($formattedDate, $this->holidays);
    }


    private function isWeekend($date)
    {
        $dayOfWeek = $date->format('w');
        return $dayOfWeek == 0 || $dayOfWeek == 6;
    }

    public function getNextBusinessDay($date)
    {
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }

        $initialDayIsBusinessDay = !$this->isWeekend($date) && !$this->isHoliday($date);

        $businessDaysNeeded = $initialDayIsBusinessDay ? 1 : 2;
        $businessDaysCount = 0;

        while ($businessDaysCount < $businessDaysNeeded) {
            $date->modify('+1 day');

            if (!$this->isWeekend($date) && !$this->isHoliday($date)) {
                $businessDaysCount++;
            }
        }

        return $date->format('Y-m-d');
    }

    public function getNextBusinessDaySuspensionNotice($date)
    {
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }

        $initialDayIsBusinessDay = !$this->isWeekend($date) && !$this->isHoliday($date);

        $businessDaysNeeded = $initialDayIsBusinessDay ? 2 : 3;
        $businessDaysCount = 0;

        while ($businessDaysCount < $businessDaysNeeded) {
            $date->modify('+1 day');

            if (!$this->isWeekend($date) && !$this->isHoliday($date)) {
                $businessDaysCount++;
            }
        }

        return $date->format('Y-m-d');
    }
}

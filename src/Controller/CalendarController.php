<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendarController extends AbstractController
{
    // ? значит что, month может быть NULL
    #[Route('/calendar/{viewType}/{month?}', name: 'app_calendar', defaults: ['viewType' => 'list', 'month' => null])]
    public function index(Request $request, string $viewType, ?int $month): Response
    {
        $currentDate = new \DateTime();
        $dayOfToday = date('Y-m-d');

        if ($month) {
            $currentDate->setDate($currentDate->format('Y'), $month, 1);
        }

        $firstDayOfMonth = (clone $currentDate)->modify('first day of this month'); //Среда
        $firstDayOfWeek = (int) $firstDayOfMonth->format('N'); // 3 - среда

        //Количество дней в месяце
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentDate->format('m'), $currentDate->format('Y'));
        $calendarDays = [];
        $today = new \DateTime();
        // Добавлять свободные места до первого дня месяца
        for ($i = 1; $i < $firstDayOfWeek; $i++) {
            $calendarDays[] = null;
        }

        // Добавлять остальные дни
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = (clone $currentDate)->setDate($currentDate->format('Y'), $currentDate->format('m'), $day);

            $isToday = $date->format('Y-m-d') === $today->format('Y-m-d');

            $calendarDays[] = [
                'date' => $date,
                'isWeekend' => in_array($date->format('N'), [6, 7]), // Sábado y domingo
                'isToday' => $isToday
            ];
        }

        $weeks = array_chunk($calendarDays, 7);

        return $this->render("calendar/" . $viewType . ".html.twig", [
            'weeks' => $weeks,
            'currentDate' => $currentDate,
        ]);
    }

}

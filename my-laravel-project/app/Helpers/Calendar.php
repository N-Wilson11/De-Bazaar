<?php

namespace App\Helpers;

use Carbon\Carbon;

class Calendar
{
    /**
     * Render a month calendar
     *
     * @param int $year
     * @param int $month
     * @param callable $dayCallback Function to render each day
     * @return string
     */
    public static function renderMonth(int $year, int $month, callable $dayCallback): string
    {
        $firstDayOfMonth = Carbon::createFromDate($year, $month, 1);
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        
        // Get the first day of the first week (might be in previous month)
        $firstDay = $firstDayOfMonth->copy()->startOfWeek();
        
        // Get the last day to display (might be in next month)
        $lastDay = $lastDayOfMonth->copy()->endOfWeek();
        
        // Create the table
        $calendar = '<table class="table table-bordered calendar-table">';
        
        // Calendar header with month and year
        $calendar .= '<caption class="text-center fw-bold mb-2">';
        $calendar .= $firstDayOfMonth->translatedFormat('F Y');
        $calendar .= '</caption>';
        
        // Days of the week
        $calendar .= '<thead><tr class="calendar-weekday-row">';
        $dayLabels = ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'];
        foreach ($dayLabels as $dayLabel) {
            $calendar .= '<th>' . __($dayLabel) . '</th>';
        }
        $calendar .= '</tr></thead>';
        
        // Start the table body
        $calendar .= '<tbody>';
        
        // Generate weeks
        $calendar .= '<tr>';
        $currentDay = $firstDay->copy();
        $dayOfWeek = 1;
        
        while ($currentDay <= $lastDay) {
            // If we're at the start of the week, start a new row
            if ($dayOfWeek > 7) {
                $calendar .= '</tr><tr>';
                $dayOfWeek = 1;
            }
            
            // Add class for days outside the current month
            $dayClass = $currentDay->month !== $month ? 'calendar-other-month' : '';
            
            // Let the callback render the day content
            $calendar .= '<td class="' . $dayClass . '">';
            $calendar .= $dayCallback($currentDay);
            $calendar .= '</td>';
            
            // Move to the next day
            $currentDay->addDay();
            $dayOfWeek++;
        }
        
        // Complete the row with empty cells if needed
        while ($dayOfWeek <= 7) {
            $calendar .= '<td class="calendar-other-month"></td>';
            $dayOfWeek++;
        }
        
        // Complete the table
        $calendar .= '</tr></tbody></table>';
        
        return $calendar;
    }
}

<?php
namespace App\classes;
use PdfReport;
use App\User;
class ReportsGeneratorPdf{
	
public static function displayReport($fromDate,$toDate,$sortBy) {
    // Retrieve any filters
  

    // Report title
    $title = 'Registered User Report';

    // For displaying filters description on header
    $meta = [
        'Registered on' => $fromDate . ' To ' . $toDate,
        'Sort By' => $sortBy
    ];

    // Do some querying..
    $queryBuilder = User::select(['name', 'email', 'created_at'])
                        ->whereBetween('created_at', [$fromDate, $toDate])
                        ->orderBy($sortBy);

    // Set Column to be displayed
    $columns = [
        'Name' => 'name',
        'Registered At', // if no column_name specified, this will automatically seach for snake_case of column name (will be registered_at) column from query result
        'User Accounts' => 'email',
        'Status' => function($result) { // You can do if statement or any action do you want inside this closure
            return ($result->email = 'admin@tamisemi.go.tz') ? 'Super Admin' : 'Client User';
        }
    ];

    /*
        Generate Report with flexibility to manipulate column class even manipulate column value (using Carbon, etc).

        - of()         : Init the title, meta (filters description to show), query, column (to be shown)
        - editColumn() : To Change column class or manipulate its data for displaying to report
        - editColumns(): Mass edit column
        - showTotal()  : Used to sum all value on specified column on the last table (except using groupBy method). 'point' is a type for displaying total with a thousand separator
        - groupBy()    : Show total of value on specific group. Used with showTotal() enabled.
        - limit()      : Limit record to be showed
        - make()       : Will producing DomPDF / SnappyPdf instance so you could do any other DomPDF / snappyPdf method such as stream() or download()
    */
    return PdfReport::of($title, $meta, $queryBuilder, $columns)
                    ->editColumn('Registered At', [
                        'displayAs' => function($result) {
                            return $result->registered_at->format('d M Y');
                        }
                    ])
                    ->editColumn('Total Balance', [
                        'displayAs' => function($result) {
                            return $result->email;
                        }
                    ])
                    ->editColumns(['Total Balance', 'Status'], [
                        'class' => 'right bold'
                    ])
                    ->showTotal([
                        'Total Balance' => 'point' // if you want to show dollar sign ($) then use 'Total Balance' => '$'
                    ])
                    ->limit(20)
                    ->stream(); // or download('filename here..') to download pdf
}	
	
}



?>
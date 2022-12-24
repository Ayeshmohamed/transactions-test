<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Set Index Array
        $transactions = array();

        // Files name that will fetch data from it
        $files = ['DataW', 'DataX', 'DataY'];
        foreach ($files as $file) {
            $file_content = file_get_contents(base_path($file . '.json'));
            array_push($transactions, json_decode($file_content));
        }

        // Filter data with status , currency
        $results = collect($transactions)->filter(function ($transaction) use ($request) {

            // Filter by status
            $statusFeatured = true;
            if ($request->status) {
                switch ($request->status) {
                    case 'paid':
                        $statusFeatured = (isset($transaction->status) && $transaction->status == 'done') ? true : false;
                        break;
                    case 'pending':
                        $statusFeatured = (isset($transaction->status) && $transaction->status == '100') ? true : false;
                        break;
                    case 'reject':
                        $statusFeatured = !isset($transaction->status) ? true : false;
                        break;
                    default:
                        # code...
                        break;
                }
            }

            // filter by currency
            $currencyFeatured = true;
            if ($request->currency) {
                $currencyFeatured = ($transaction->currency == $request->currency) ? true : false;
            }

            return ($statusFeatured && $currencyFeatured) ? $transaction : null;
        });

        return response($results, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
}

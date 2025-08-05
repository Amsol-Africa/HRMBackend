use Illuminate\Support\Facades\Session;
use Illuminate\Console\Scheduling\Schedule;

protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        Session::start();
        $slug = Session::get('active_business_slug');
        if (!$slug) {
            $business = Business::find(1);
            if ($business) {
                Session::put('active_business_slug', $business->slug);
            }
        }
        app()->call('App\Http\Controllers\EmployeeController@sendAutomatedContractReminders');
    })->dailyAt('15:50');
}

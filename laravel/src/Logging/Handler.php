<?php
namespace L4Logger\Laravel\Logging;
// use Illuminate\Log\Logger;
use DB;
use Illuminate\Support\Facades\Auth;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class Handler extends AbstractProcessingHandler{
/**
 *
 * Reference:
 * https://github.com/markhilton/monolog-mysql/blob/master/src/Logger/Monolog/Handler/MysqlHandler.php
 */
    public function __construct($level = Logger::DEBUG, $bubble = true) {
        $this->table = 'logs';
        parent::__construct($level, $bubble);
    }
    protected function write(array $record):void
    {
       $data = array(
           'app_name'         => config('app.name'),
           'app_env'          => config('app.env'),
           'app_url'          => config('app.url'),
           'url'              => request()->url(),
           'post_params'        => \Request::post(),
           'get_params'         => \Request::post(),
           'session'          => session()->all(),
           'user'             => Auth::user(),
           'key'              => config('logging.channels.l4logger.key'),
           'message'          => $record['message'],
           'context'          => json_encode($record['context']),
           'level'            => $record['level'],
           'log_type'         => $record['level_name'],
           'channel'          => $record['channel'],
           'record_datetime'  => $record['datetime']->format('Y-m-d H:i:s'),
           'extra'            => json_encode($record['extra']),
           'formatted'        => $record['formatted'],
           'remote_addr'      => $_SERVER['REMOTE_ADDR'] ?? 'NA',
           'user_agent'       => $_SERVER['HTTP_USER_AGENT'] ?? 'NA',
           'created_at'       => time(),
           'server_data'      => $_SERVER,
       );

       try {
         
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://us-central1-l4logger.cloudfunctions.net/l4logger/add",
          // CURLOPT_URL => "http://localhost:3000/add",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/json",
            "postman-token: f20715a8-14f9-2d96-53d4-6bc260596c32"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          // echo "cURL Error #:" . $err;
        } else {
          // echo $response;
        }
       // dd(json_encode($data));

      } catch (\Exception $e) {
        
      }

    }
}
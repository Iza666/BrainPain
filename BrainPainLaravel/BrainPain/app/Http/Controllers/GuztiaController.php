<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Galdera;
use App\User;
use DB;

class GuztiaController extends Controller
{
    public function index()
    {
        $galdera = Galdera::orderByRaw("RAND()")->get()->take(1);
        //$erantzunRandom = DB::table('galderak')->0select('opt1_erantzuna', 'opt2', 'opt3')->where('id', $galdera->id);

        //echo $erantzunRandom;
        return response()->json($galdera, 200);
    }
    public function GetRanking()
    {
        $sailkapena = DB::table('partidak')
                    ->join('users', 'users.id', '=', 'partidak.id_erabiltzailea')
                    ->select('id_erabiltzailea', 'users.id', 'users.erabiltzailea', DB::raw('sum(puntuak) as Totala'))
                    ->groupBy('id_erabiltzailea', 'users.id', 'users.erabiltzailea')->get();
        
        return response()->json($sailkapena, 200);
    }
    //Galdera sartu BADABIL, partidak taularekin kombinatu behar da 
    public function insertQuestion()
    {
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            DB::table('erabiltzaile_galderak')->insert(
                ['id_erabiltzailea' => $request->id_erabiltzailea,
                 'id_galdera' => $request->id_galdera,
                 'id_partida' => $request->id_partida,
                 'erantzuna' => $request->erantzuna]
            );
        }
    }
    //Partida sartu PROVISIONAL
    public function insertMatch()
    {
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            DB::table('partidak')->insert(
                ['id' => $request->id,
                 'id_erabiltzailea' => $request->id_erabiltzailea,
                 'data' => $request->data,
                 'puntuak' => $request->puntuak,
                 'zenbat_zuzen' => $request->zenbat_zuzen,
                 'zenbat_denbora' => $request->zenbat_denbora]
            );
        }
    }
    //Profila aldatu PROVISIONAL
    public function changeProfile(){
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            User::find($request->id)->update(
                [
                 'erabiltzailea' => $request->erabiltzailea,
                 'email' => $request->email,
                ]
            );
        }
    }
    public function logedPersonMatch(){
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $partida = DB::table('partidak')->
                       select(DB::raw("*"))->
                       where('id_erabiltzailea',$request->id_erabiltzailea)->
                       where('data',$request->data)->get();
            $denbora = explode(":", $request->zenbat_denbora);
            $minutuakSegundutan = floor(($denbora[0] / 60) % 60);
            $totala = $minutuakSegundutan + $denbora[1];
            $puntuFinal = $request->puntuak / $totala;
            DB::table('partidak')->
                where('id_erabiltzailea',$request->id_erabiltzailea)->
                where('data',$request->data)->
                update(['puntuak'=>$puntuFinal]);
            DB::table('partidak')->
                where('id_erabiltzailea',$request->id_erabiltzailea)->
                where('data',$request->data)->  
                update(['zenbat_zuzen'=>$request->zenbat_zuzen]);
            DB::table('partidak')->
                where('id_erabiltzailea',$request->id_erabiltzailea)->
                where('data',$request->data)->    
                update(['zenbat_denbora'=>$request->zenbat_denbora]);   
        }
    }
    //Erabiltzailearen datuak hartuko ditu login egiterakoan eta partidak taulan komparatuz, egun horretako 
    //data badu ezin izango du jolastu eta true edo false bueltatuko du
    public function dailyMatchController(){
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
        }
    }
    public function getUser(){
        $user = auth()->user()->id;

    }

    /*


            JONARE

    public function getUserTaldea(Request $request) 
      {
         //$name = $request->input('name');
-        $user = $request->user();
+        //$user = $request->user();
        
-        $user = Auth::user();
+        $user = auth()->user()->id;
+
+        //$user2 = AuthController.user;
 
         //echo ($user);
          $userTaldea = DB::table('taldeak_user')->where('taldea_id','=','2')->get();


          */
}

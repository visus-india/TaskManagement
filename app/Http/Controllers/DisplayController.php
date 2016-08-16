<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Table_Column_Names;
use App\Activists;
use App\Projects;
use App\Clients;
use App\KeyValues;
use App\ProjectActivity;
use App\ActivityList;
use Validator;
use DB;
use Log;
class DisplayController extends Controller
{

  public function view()
  {

          return view('welcome');
  }
    public function viewTasks()
    {
      $taskSearch = "true";
      $tableColumns = Table_Column_Names::orderBy('ID', 'asc')
      -> where ('MODULE','=','TASK')->get();
      // $projectactivityDisplay =ProjectActivity::orderBy('PROJECT_ACTIVITY.LASTUPDATEDATE','desc')
      // -> join ('ACTIVISTS', 'PROJECT_ACTIVITY.ACTIVISTID', '=', 'ACTIVISTS.ID')
      // ->select('PROJECT_ACTIVITY.*','ACTIVISTS.FIRST_NAME as FIRST_NAME','ACTIVISTS.LAST_NAME as LAST_NAME', 'ACTIVISTS.ID as ActivistID1')
      // ->get();
$clientsearch =  Clients::orderBy('ID', 'asc') ->get();
        $projectSearch =  Projects::orderBy('ID', 'asc') ->get();


      	return view('welcome', ['taskSearch' => $taskSearch, 'projectSearch' =>$projectSearch, 'clientsearch' =>$clientsearch,
            'tableColumns' => $tableColumns]);

      }

      public function searchProject(Request $req)
      {
        $projectactivityDisplay =ProjectActivity::orderBy('PROJECT_ACTIVITY.LASTUPDATEDATE','desc')
        -> join ('ACTIVISTS', 'PROJECT_ACTIVITY.ACTIVISTID', '=', 'ACTIVISTS.ID')
        ->select('PROJECT_ACTIVITY.*','ACTIVISTS.FIRST_NAME as FIRST_NAME','ACTIVISTS.LAST_NAME as LAST_NAME', 'ACTIVISTS.ID as ActivistID1')
        ->where ('PROJECTID','=',$req->projectsearch)
        ->get();

        $activistsListDisplay = Activists :: orderby('LASTUPDATEDATE','desc')
        ->where ('ACTIVE_STATUS' ,'=', 'Y') ->get();
                      Log::info ('in activist list'.$projectactivityDisplay);
        $taskSearch = "true";
        $tableColumns = Table_Column_Names::orderBy('ID', 'asc')
        -> where ('MODULE','=','TASK')->get();
        $statusValue = KeyValues :: orderBy('SEQUENCE' ,'asc')
          ->where ('KEYVALUETYPE','=', 'Status') ->get();
          $selected = new Projects;
          $selected  = Projects::orderBy('PROJECTS.LASTUPDATEDATE','desc')
          -> join ('CLIENTS', 'PROJECTS.CLIENTID', '=', 'CLIENTS.ID')
          ->where ('PROJECTS.ID','=',$req->projectsearch )
          -> where ('CLIENTID', '=', $req->clientsearch)
                      -> get();

        $clientsearch =  Clients::orderBy('ID', 'asc') ->get();
          $projectSearch =  Projects::orderBy('ID', 'asc') ->get();
        return view('welcome', ['selected' => $selected, 'statusValue' => $statusValue, 'taskSearch' => $taskSearch, 'projectSearch' =>$projectSearch, 'clientsearch' =>$clientsearch,
            'tableColumns' => $tableColumns,
            'projectactivityDisplay'=>$projectactivityDisplay,
          'activistsListDisplay' => $activistsListDisplay]);

      }
      public function viewActivityList()
      {

        $activityList = ActivityList::orderBy('LASTUPDATEDDATE', 'desc')->get();
        $activityListLabel  = Table_Column_Names::orderBy('ID', 'asc')
        -> where ('MODULE','=','ACTIVITYLIST')
        -> where ('COLUMN_NAME', '!=', 'S.NO')
        -> where ('COLUMN_NAME', '!=', 'ACTIVESTATUS')->get();

        $categoryList = KeyValues::orderBy('LASTUPDATEDDATE', 'desc')
        ->where('KEYVALUETYPE','=','Category')->get();

        Log::info("in add activityList".$categoryList );
        $fieldDisabled ='';
        	return view('welcome', ['categoryList' =>$categoryList,
              'activityList' => $activityList,'activityListLabel' => $activityListLabel,'fieldDisabled' =>$fieldDisabled]);

        }

        public function viewKeyValueList()
        {

          $keyValueList = KeyValues::orderBy('LASTUPDATEDDATE', 'desc')->get();
          $keyValueLabel = Table_Column_Names::orderBy('ID', 'asc')
          -> where ('MODULE','=','KEYVALUE')
          -> where ('COLUMN_NAME', '!=', 'ACTIVESTATUS')->get();
          Log::info("in add KeyValue".$keyValueLabel);

            return view('welcome', [
                'keyValueList' => $keyValueList,'keyValueLabel' => $keyValueLabel]);

          }


          public function saveKeyValue(Request $req)
          {
            $keyValue = new KeyValues;
            $keyValue ->fill($req->all());
            $countKeyValues  = count(KeyValues::orderBy('KEYVALUETYPE','asc')
                              ->where('KEYVALUETYPE','=', $req->KEYVALUETYPE)
                              ->get());

            $keyValue ->SEQUENCE = $countKeyValues+1;
            Log::info("before validating keyvalue".$countKeyValues);
            $validator = Validator::make($req->all(), [
              'KEYVALUE' => 'required',
              'KEYVALUETYPE' => 'required'
          ]);

              Log::info("before save".$keyValue);

            Log::info("in save keyvalue".$keyValue);
            if ($validator->fails()) {
                Log::info('here validator fails');
            return redirect('/viewKeyValueList')
                        ->withErrors($validator)
                        ->withInput();
        } else {
            $keyValue->save();
          return DisplayController:: viewKeyValueList();
        }


          }

          public function editKeyValue(Request $req, $id)
          {
            $keyValue = new KeyValues;
            $keyValue = KeyValues::find($id);
            $keyValue ->fill($req->all());

              Log::info("before save".$keyValue);
            $keyValue->save();
            Log::info("in save keyvalue".$keyValue);
            return DisplayController:: viewKeyValueList();

          }



          public function saveProject(Request $req)
          {
            $project = new Projects;
            $project ->fill($req->all());

            $validator = Validator::make($req->all(), [
              'PROJECTNAME' => 'required',
              'CLIENTID' => 'required'
          ]);

            if ($validator->fails()) {
                Log::info('here validator fails');
            return redirect('/viewProjectList')
                        ->withErrors($validator)
                        ->withInput();
          } else {
            Log::info("before project save".$project);
          $project->save();
          Log::info("in save project".$project);
          return DisplayController:: viewProjectList();
          }



          }


          public function editProjectList(Request $req,$id)
          {
            Log::info ("in edit".$req);

            $project = Projects::find($id);
              $project ->fill($req->all());

              Log::info("before project save".$project);
            $project->save();
            Log::info("in save project".$project);
            return DisplayController:: viewProjectList();

          }





          public function saveClient(Request $req)
          {
            $client = new Clients;
            $client ->fill($req->all());

            $validator = Validator::make($req->all(), [
              'CLIENTNAME' => 'required',
              'ADDRESS' => 'required',
              'CLIENTEMAILID' => 'required',
              'CONTACTTITLE' => 'required',
              'CONTACTFIRSTNAME' => 'required',
              'CONTACTLASTNAME' => 'required',
              'CONTACTEMAILID' => 'required',
              'MOBILENO' => 'required',
                'FAXNO' => 'required',
                  'PHONENO' => 'required',
                  'FAXNO' => 'required'
            ]);

            if ($validator->fails()) {
                Log::info('here validator fails');
                return redirect('/viewClientList')
                        ->withErrors($validator)
                        ->withInput();
            } else {
              Log::info("before client save".$client);
            $client->save();
            Log::info("in save keyvalue".$client);
            return DisplayController:: viewClientList();
            }



          }

          public function editClientList(Request $req, $Id)
          {
            $client = new Clients;
            $client = Clients::find($Id);
            $client ->fill($req->all());

              Log::info("before client edit".$client);
            $client->save();
            Log::info("in save keyvalue".$client);
            return DisplayController:: viewClientList();

          }



          public function saveActivityList(Request $req)
          {

            $activityList = new ActivityList;
            $activityList ->fill($req->all());

            $validator = Validator::make($req->all(), [
              'CATEGORY' => 'required',
              'ACTIVITY' => 'required'
          ]);

            if ($validator->fails()) {
                Log::info('here validator fails');
                if(Request::ajax())
              {
                  return Response::json(array('errors' => $messages));
              }
              else
              {
                  return Redirect::back()->withInput()->withErrors($validation);
              }
        } else {



              Log::info("before activity list save".$activityList);
            $activityList->save();
            Log::info("in save activity list".$activityList);
            return DisplayController:: viewActivityList();

          }
        }
          public function editActivityList(Request $req, $Id)
          {
            $activityList = new ActivityList;
            $activityList = ActivityList::find($Id);
            $activityList ->fill($req->all());

              Log::info("before activity list edit".$activityList);
            $activityList->save();
            Log::info("after save activity list edit".$activityList);
            return DisplayController:: viewActivityList();

          }




          public function viewClientList()
          {

            $clientsList = Clients::orderBy('LASTUPDATEDATE', 'desc')->get();
            $clientLabel = Table_Column_Names::orderBy('ID', 'asc')
            -> where ('MODULE','=','CLIENT')
            -> where ('COLUMN_NAME', '!=', 'S.NO')
            -> where ('COLUMN_NAME' , '!=' ,'ACTIVESTATUS')->get();
            Log::info("in add client".$clientLabel);
              return view('welcome', [
                  'clientsList' => $clientsList,'clientLabel' => $clientLabel]);

            }
            public function viewProjectList()
            {
              $clientNames= Clients::orderBy('ID', 'asc')->get();
              $listProjects = Projects::orderBy('PROJECTS.LASTUPDATEDATE','desc')
              -> join ('CLIENTS', 'PROJECTS.CLIENTID', '=', 'CLIENTS.ID')
                          -> get(['PROJECTS.ID','CLIENTNAME','PROJECTNAME', 'PROJECTS.ACTIVESTATUS']);
                 Log::info ("list projects ". $listProjects);
                 $projectLabel = Table_Column_Names::orderBy('ID', 'asc')
                 -> where ('MODULE','=','PROJECT')
                 -> where ('COLUMN_NAME', '!=', 'S.NO')
                 -> where ('COLUMN_NAME','!=','CLIENTNAME')
                 ->where ('COLUMN_NAME', '!=', 'ACTIVESTATUS')->get();
                 Log::info("in add Project".$projectLabel);

                return view('welcome', [
                    'clientNames'=> $clientNames,'listProjects' => $listProjects,'projectLabel' => $projectLabel]);

              }


    public function screenSetup()
    {
      $tableColumns = Table_Column_Names::orderBy('MODULE', 'desc')
      ->get();

      return view('screenSetup',[
           'tableColumns' => $tableColumns]);

    }
    public function columnNameUpdate(Request $req, $ID)
    {
      $tableColumn = Table_Column_Names::find($ID);
      $tableColumn = $tableColumn->fill($req->all());
      $tableColumn->save();
      $tableColumns = Table_Column_Names::orderBy('MODULE', 'desc')
      ->get();
      return view('screenSetup',[
           'tableColumns' => $tableColumns]);

    }


    public function displayActivists()
    {
      $activistColumns = Table_Column_Names::orderBy('ID', 'asc')
      -> where ('MODULE','=','ACTIVIST')
      -> where ('COLUMN_NAME', '!=', 'ACTIVE_STATUS')
      -> where ('COLUMN_NAME', '!=', 'S.NO')->get();
      $activists= Activists :: orderby('LASTUPDATEDATE','desc')
      ->get();
      $activistLabel = Table_Column_Names::orderBy('ID', 'asc')
      -> where ('MODULE','=','ACTIVIST')
      -> where ('COLUMN_NAME', '!=', 'ACTIVE_STATUS')
      -> where ('COLUMN_NAME', '!=', 'S.NO')
      ->get();
      Log::info('in the display'.$activists);
      return view('welcome', [
          'activists' => $activists,'activistColumns' => $activistColumns,'activistLabel' => $activistLabel]);

    }


    public function saveActivists(Request $req)
    {
      $activist = new Activists;
      $activist ->fill($req->all());

      $validator = Validator::make($req->all(), [
        'TITLE' => 'required',
        'FIRST_NAME' => 'required',
        'LAST_NAME' => 'required',
        'EMAILID' => 'required',
        'MOBILE_NUMBER' => 'required'
      ]);

      if ($validator->fails()) {
          Log::info('here validator fails');
          return redirect('/Activists')
                  ->withErrors($validator)
                  ->withInput();
      } else {
        Log::info("before save".$activist);
      $activist->save();
      Log::info("in save".$activist);
      return DisplayController:: displayActivists();
      }

    }

    public function editActivist(Request $req, $id)
    {


      // $activist ->fill($req->all());
        Log::info("before edit".($req));
    Activists:: where ('ID', $id) -> update(['ACTIVE_STATUS' => $req->ACTIVE_STATUS]) ;
    $activist = Activists::find($id);
    Log::info(  $activist);
      return DisplayController:: displayActivists();

    }
    public function updateActivist(Request $req, $id)
    {
      $activist =  Activists::find($id);
      $activist ->fill($req->all());

        Log::info("before save".$activist);
      $activist->save();
      Log::info("in update".$activist);
      return DisplayController:: displayActivists();

    }

    public function viewActivist(Request $req, $id)
    {
      $activist =  Activists::find($id);
      $activistLabel = Table_Column_Names::orderBy('ID', 'asc')
      -> where ('MODULE','=','ACTIVIST')
      -> where ('COLUMN_NAME', '!=', 'S.NO')->get();
        $activistLabel = $activistLabel ->pull('COLUMN_NAME');

        Log::info("in activist".$activist);
      Log::info("in view".$activistLabel);
      return view('activist',[
           'activistLabel' => $activistLabel,'activist' => $activist]);

    }

    public function projectSetup()
    {
      $projects =  Projects::orderBy('ID', 'asc') ->get();
      $clients =  Clients::orderBy('ID', 'asc') ->get();
      $category = ActivityList::groupBy('CATEGORY') ->get();
      $activists = Activists::orderBy('ID', 'asc')-> where ('ACTIVE_STATUS' ,'=', 'Y') ->get();
      Log::info('in project setup'.$activists);
      $tableColumns = Table_Column_Names::orderBy('ID', 'asc')
      -> where ('MODULE','=','TASK')->get();
      return view('welcome',[
           'projects' => $projects,'clients' => $clients,'category' =>$category,'tableColumns'=>$tableColumns,'activists' =>$activists]);
    }

    public function addProjectActivity(Request $request)
    {
        $project_activity = new ProjectActivity;
        $project_activity= ProjectActivity:: find($request->ID);
        $project_activity ->fill($request->all());
        $project_activity->save();
      Log::info("here in add project activity".$project_activity);
      return DisplayController::viewTasks();

    }


    public function createProjectSetup(Request $request)
    {
      $selectedProject =$request->project;
      $selectedClient=$request->client;
      $statusValue = KeyValues :: orderBy('SEQUENCE' ,'asc')
        ->where ('KEYVALUETYPE','=', 'Status') ->get();
      $activistID =$request->activistId;
      $client = new Clients;
      $client = Clients::find($selectedClient);
      foreach($request->category as $category)
	{
    $activity=ActivityList::orderBy('ID','asc')
    ->where ('CATEGORY','=',$category) ->get();
    Log::info ("in activity ".$category);
      foreach ($activity as $activity ) {
        Log::info("here in for loop activity" .$activity);
        $project_activity = new ProjectActivity;
        $project_activity->PROJECTID =$selectedProject;
        $project_activity->ACTIVITY=$activity->ACTIVITY;
        $project_activity->CATEGORY =$activity->CATEGORY;
        $project_activity->ACTIVISTID =$activistID;
        $project_activity->CONTACTTITLE = $client ->CONTACTTITLE;
        $project_activity->CONTACTFIRSTNAME = $client ->CONTACTFIRSTNAME;
        $project_activity->CONTACTLASTNAME = $client ->CONTACTLASTNAME;
          $project_activity->CONTACTEMAILID = $client ->CONTACTEMAILID;
            $project_activity->MOBILENO = $client ->MOBILENO;
              $project_activity->PHONENO = $client ->PHONENO;
  $project_activity->FAXNO = $client ->FAXNO;
        $project_activity->save();
        Log::info("after save".$project_activity);

      }
    }

    $tableColumns = Table_Column_Names::orderBy('ID', 'asc')
    -> where ('MODULE','=','TASK')->get();
      $projectactivity  = new ProjectActivity;
      $projectactivity =ProjectActivity::orderBy('PROJECT_ACTIVITY.LASTUPDATEDATE','desc')
      -> join ('ACTIVISTS', 'PROJECT_ACTIVITY.ACTIVISTID', '=', 'ACTIVISTS.ID')
      ->select('PROJECT_ACTIVITY.*','ACTIVISTS.FIRST_NAME as FIRST_NAME','ACTIVISTS.LAST_NAME as LAST_NAME', 'ACTIVISTS.ID as ActivistID1')
      ->where('PROJECTID','=',$selectedProject)
      ->get();
      $selected = new Projects;
      $selected  = Projects::orderBy('PROJECTS.LASTUPDATEDATE','desc')
      -> join ('CLIENTS', 'PROJECTS.CLIENTID', '=', 'CLIENTS.ID')
      ->where ('PROJECTS.ID','=',$selectedProject )
      -> where ('CLIENTID', '=', $selectedClient)
                  -> get();

      Log::info ("before assigning project Name ". $projectactivity);
      $activistsListAdded = Activists :: orderby('LASTUPDATEDATE','desc')
      ->where ('ACTIVE_STATUS' ,'=', 'Y') ->get();

      return view('welcome',['activistsListAdded' =>$activistsListAdded,
          'selected' => $selected ,'statusValue' => $statusValue, 'tableColumns'=>$tableColumns,'projectactivity' =>$projectactivity]);

    }

}

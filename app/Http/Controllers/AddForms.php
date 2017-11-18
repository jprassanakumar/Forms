<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Psy\Util\Json;
use DB;
use App\FormLog;
use App\FormSkeleton;
use App\FormElements;
use App\Forms;
use App\FormData;


class AddForms extends Controller
{   
	function fileUploader(Request $request) 
	{
		try 
		{
			
			Log::info($request['data']);
			
			if ( 0 < $_FILES['file']['error'] ) {
			
				echo 'Error: ' . $_FILES['file']['error'] . '<br>';
			}
			else 
			{
				
				$output=array();

				$firstRun=true;
				$data=$request['data'];
				$data=explode("&", $data);
				foreach($data as $val) 
			{ 
				if($val=='')
					continue;

	        	 $val=explode("=",$val);
	             
	            $output [ $val[0]]=$val[1];
 

	        }
	        $formId=$output['form_id'];

	            $form=new FormLog();
				$form->form_id=$formId;

				$form->save();

				array_shift($output);
				foreach ($output as $key => $value) 
	            {
	            	$skeletonId=$formId."_".substr($key, 0, -3);

	            	$skeletonId=DB::select("select * from FormSkeleton where form_id=".$formId." and element_unqiue_id='".$skeletonId."'");

	            		if($skeletonId==null)
	            			continue;

	            	$formData=new FormData();

	            	$formData->element_skeleton_id=$skeletonId[0]->id;

	            	$formData->form_instance_id=$form->id;

	            	$formData->data=$value;

	            	$formData->save();

	            }
				
				$cmd_string="mkdir -p ".$_SERVER["DOCUMENT_ROOT"]."/fileupload/$formId/".$form->id."/";
				Log::info($cmd_string);
				exec($cmd_string, $output);
				move_uploaded_file($_FILES['file']['tmp_name'], "fileupload/$formId/".$form->id."/". $_FILES['file']['name']);
				$skeletonId=$formId."_fileupload_1";
					Log::info($skeletonId);
	            	$skeletonId=DB::select("select * from FormSkeleton where form_id=".$formId." and element_unqiue_id='".$skeletonId."'");


	            	$formData=new FormData();

	            	$formData->element_skeleton_id=$skeletonId[0]->id;

	            	$formData->form_instance_id=$form->id;

	            	$formData->data="fileupload/$formId/".$form->id."/".$_FILES['file']['name'];

	            	$formData->save();
					$response = [
							'response' => 'success',
							];

			}
		} 
		catch (\Exception $e) 
		{
			Log::info($e);
			$response = [
					
							'response' => 'failure',
							'message' => 'Unable to add data'
					
			];
		}
		return $response;
	}
	function addData(Request $request)
	{
		try 
		{
			$output =array(); 
        	$firstRun = true; 
			foreach($_GET as $key=>$val) 
			{ 
	        	 
	            $output [ $key]=$val;

	        }

	        	$formId=$output['form_id'];

	            $form=new FormLog();
				$form->form_id=$formId;

				$form->save();

				array_shift($output);
	            foreach ($output as $key => $value) 
	            {
	            	$skeletonId=$formId."_".substr($key, 0, -3);

	            	$skeletonId=DB::select("select * from FormSkeleton where form_id=".$formId." and element_unqiue_id='".$skeletonId."'");

	            		if($skeletonId==null)
	            			continue;

	            	$formData=new FormData();

	            	$formData->element_skeleton_id=$skeletonId[0]->id;

	            	$formData->form_instance_id=$form->id;

	            	$formData->data=$value;

	            	$formData->save();

	            }

			$response = [
							'response' => 'success',
							

							];
	       
         } 
		catch (\Exception $e) 
		{
			$response = [
							'response' => 'failure',
							'message'=>'Unable to add data'

							];
			Log::info($e);
		}
		return $response;
	}

	function getForms()
	{
		try 
		{
			$forms=DB::select("select * from Forms");

			foreach ($forms as  $value) 
			{
				echo "<a href='/api/showFormData?id=".$value->id."'>".$value->form_name."</a><br>";
			}
		} 
		catch (\Exception $e) 
		{
			
		}
	}
	function showFormData(Request $request)
	{
		try 
		{
			$id=$request->get('id');

			$formInstances=DB::select("select id from FormLog where form_id=$id");

			foreach ($formInstances as  $value) 
			{
				$formData=DB::select("select data,element_unqiue_id from FormData join FormSkeleton on FormSkeleton.id=element_skeleton_id where form_instance_id=".$value->id);
				$data='';
				foreach ($formData as $dataValue) 
				{

					echo $dataValue->element_unqiue_id." -> ".$dataValue->data."<br>";
				}
				echo "------<br>";
			}
			
		} 
		catch (\Exception $e) 
		{
			Log::info($e);

		}

	}
	function addForms(Request $request)
	{
		try 
		{
			$formPath=$_SERVER["DOCUMENT_ROOT"]."/Forms/";
			if (!file_exists($formPath)) {
				mkdir($formPath, 0777, true);
			}
			

			$form=new Forms();
			$form->form_name=$request['name'];

			$form->save();

			

			foreach ($request['elements'] as $key=> $value) 
			{
				if($value==null)
					continue;
				$formSkeleton=new FormSkeleton();
				$formSkeleton->form_id=$form->id;
				$temp=explode("_",$value);
				$elementID=DB::select("select * from FormElements where element='".$temp[0]."'");
				Log::info("select * from FormElements where element='".$temp[0]."'");
				$formSkeleton->element_id=$elementID[0]->id;
				$formSkeleton->element_unqiue_id=$form->id."_".$value;
				$formSkeleton->save();
			}

			$fo=fopen("$formPath/".$request['name'].".html","w");

			$homePath=app_path();

			$template=file_get_contents($homePath."/template.html");

			$template=str_replace("{{FORMS}}",$request['forms'],$template);
			$template=str_replace("{{FORM_NAME}}",$request['name'],$template);
			$template=str_replace("{{FORM_ID}}",$form->id,$template);


			fputs($fo,$template,strlen($template));
			
			fclose($fo);
			Log::info($formPath);

			$response = [
							'response' => 'success',

							];
							
			

		} 
		catch (\Exception $e) 
		{
			$response = [
							'response' => 'failure',
							'message'=>'Unable to create forms'

							];
			Log::info($e);
		}
		return $response;
	}
}
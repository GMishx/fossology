<?php
/***********************************************************
 Copyright (C) 2008-2013 Hewlett-Packard Development Company, L.P.
 Copyright (C) 2015-2017 Siemens AG

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
***********************************************************/

use Fossology\Lib\Auth\Auth;
use Fossology\Lib\Dao\UploadDao;
use \delagent\ui\DeleteMessages;
use \delagent\ui\DeleteResponse;
/**
 * \file admin_upload_delete.php
 * \brief delete a upload
 */
define("TITLE_admin_upload_delete", _("Delete Uploaded File"));

/**
 * \class admin_upload_delete extend from FO_Plugin
 * \brief delete a upload, certainly, you need the permission 
 */
class admin_upload_delete extends FO_Plugin 
{
  /** @var UploadDao */
  private $uploadDao;

  function __construct()
  {
    $this->Name = "admin_upload_delete";
    $this->Title = TITLE_admin_upload_delete;
    $this->MenuList = "Organize::Uploads::Delete Uploaded File";
    $this->DBaccess = PLUGIN_DB_WRITE;
    parent::__construct();

    global $container;
    $this->uploadDao = $container->get('dao.upload');
  }
  
  public function getTemplateName()
  {
    return "admin_upload_delete.html.twig";
  }

  /**
   * \brief Given a folder_pk, try to add a job after checking permissions.
   * \param $uploadpk - the upload(upload_id) you want to delete
   *
   * \return string with the message.
   */
  function TryToDelete($uploadpk)
  {
    if(!$this->uploadDao->isEditable($uploadpk, Auth::getGroupId())){
      $returnMessage = DeleteMessages::NO_PERMISSION;
      return new DeleteResponse($returnMessage);
    }

    $rc = $this->Delete(intval($uploadpk));

    if (! empty($rc)) {
      $returnMessage = DeleteMessages::SCHEDULING_FAILED;
      return new DeleteResponse($returnMessage);
    }

    /* Need to refresh the screen */
    $URL = Traceback_uri() . "?mod=showjobs&upload=$uploadpk ";
    $LinkText = _("View Jobs");
    $returnMessage = DeleteMessages::SUCCESS;
    return new DeleteResponse($returnMessage,
      " <a href=$URL>$LinkText</a>");
  }

  /**
   * \brief Given a folder_pk, add a job.
   * \param $uploadpk - the upload(upload_id) you want to delete
   * \param $Depends - Depends is not used for now
   *
   * \return NULL on success, string on failure.
   */
  function Delete($uploadpk, $Depends = NULL) 
  {
    /* Prepare the job: job "Delete" */
    $user_pk = Auth::getUserId();
    $group_pk = Auth::getGroupId();
    $jobpk = JobAddJob($user_pk, $group_pk, "Delete", intval($uploadpk));
    if (empty($jobpk) || ($jobpk < 0)) {
      $text = _("Failed to create job record");
      return ($text);
    }
    /* Add job: job "Delete" has jobqueue item "delagent" */
    $jqargs = "DELETE UPLOAD $uploadpk";
    $jobqueuepk = JobQueueAdd($jobpk, "delagent", $jqargs, NULL, NULL);
    if (empty($jobqueuepk)) {
      $text = _("Failed to place delete in job queue");
      return ($text);
    }

    /* Tell the scheduler to check the queue. */
    $success  = fo_communicate_with_scheduler("database", $output, $error_msg);
    if (!$success) 
    {
      $error_msg = _("Is the scheduler running? Your jobs have been added to job queue.");
      $URL = Traceback_uri() . "?mod=showjobs&upload=$uploadpk ";
      $LinkText = _("View Jobs");
      $msg = "$error_msg <a href=$URL>$LinkText</a>";
      return $msg; 
    }
    return (NULL);
  } // Delete()

  /**
   * @param $uploadpks
   * @brief starts deletion and handles error messages
   * @return string
   */
  function initDeletion($uploadpks)
  {
    if(sizeof($uploadpks) <= 0)
    {
      return DisplayMessage("No uploads selected");
    }

    $V = "";
    $errorMessages = [];
    $deleteResponse = NULL;
    for($i=0; $i < sizeof($uploadpks); $i++)
    {
      $deleteResponse = $this->TryToDelete(intval($uploadpks[$i]));

      if($deleteResponse->getDeleteMessageCode() != DeleteMessages::SUCCESS)
      {
        $errorMessages[] = $deleteResponse;
      }
    }

    if(sizeof($uploadpks) == 1)
    {
      $V .= DisplayMessage($deleteResponse->getDeleteMessageString().$deleteResponse->getAdditionalMessage());
    }
    else
    {
      $displayMessage = "";

      if(in_array(DeleteMessages::SCHEDULING_FAILED, $errorMessages))
      {
        $displayMessage .= "<br/>Scheduling failed for " .
          array_count_values($errorMessages)[DeleteMessages::SCHEDULING_FAILED] . " uploads<br/>";
      }

      if(in_array(DeleteMessages::NO_PERMISSION, $errorMessages))
      {
        $displayMessage .= "No permission to delete " .
          array_count_values($errorMessages)[DeleteMessages::NO_PERMISSION]. " uploads<br/>";
      }

      $displayMessage .= "Deletion of " .
        (sizeof($uploadpks)-sizeof($errorMessages)) . " projects queued";
      $V .= DisplayMessage($displayMessage);
    }
    return $V;
  }

  /**
   * \brief Generate the text for this plugin.
   */
  public function Output()
  {
    /* If this is a POST, then process the request. */
    $uploadpks = GetParm('upload', PARM_RAW);
    if (!empty($uploadpks))
    {
      $this->vars['initDeletion'] = $this->initDeletion($uploadpks);
    }
    /* Create the AJAX (Active HTTP) javascript for doing the reply
     and showing the response. */
    $this->vars['ActiveHTTPscript'] = ActiveHTTPscript("Uploads");
    $this->vars['tracebackUri'] = Traceback_uri();
    $root_folder_pk = GetUserRootFolder();
    $this->vars['folderListOption'] = FolderListOption($root_folder_pk, 0);
    $this->vars['uploadList'] = FolderListUploads_perm($root_folder_pk, Auth::PERM_WRITE);
    return $this->render('admin_upload_delete.html.twig');
  }
}
$NewPlugin = new admin_upload_delete;

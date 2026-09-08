<?php

namespace App\Controller;

// require_once('../../vendor/upwork/php-upwork-oauth2/src/Upwork/API/constants.php');

use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\ForbiddenException;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Security;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Query;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Client;

class CompaniesController extends AppController
{
	protected $ProjectMilestones;
	protected $ProjectMilestonesLogs;
	protected $Opportunity;
	protected $Probability;
	protected $SupportPlan;
	protected $SupportPlansPayment;
	protected $ActivityTbl;
	protected $UpworkContract;
	protected $UpworkEngagementList;
	protected $UpworkMilestone;
	protected $Plans;
	protected $Stage;

	public function initialize(): void
	{
		parent::initialize();

		// $this->loadComponent('Paginator');
		$this->loadComponent('Flash'); // Include the FlashComponent
		// $this->loadComponent('RequestHandler');
		// $this->loadModel("ProjectMilestonesLogs");
		// $this->MilestoneExtend = $this->getTableLocator()->get("MilestoneExtend");

		$this->ProjectMilestones = $this->fetchTable("ProjectMilestones");
		$this->ProjectMilestonesLogs = $this->fetchTable("ProjectMilestonesLogs");
		$this->Opportunity = $this->fetchTable("Opportunity");
		$this->Probability = $this->fetchTable("Probability");
		$this->SupportPlan = $this->fetchTable("SupportPlan");
		$this->SupportPlansPayment = $this->fetchTable("SupportPlansPayment");
		$this->ActivityTbl = $this->fetchTable("Activity");
		$this->UpworkContract = $this->fetchTable("UpworkContract");
		$this->UpworkEngagementList = $this->fetchTable("UpworkEngagementList");
		$this->UpworkMilestone = $this->fetchTable("UpworkMilestone");
		$this->Plans = $this->fetchTable("Plans");
		$this->Stage = $this->fetchTable("OpportunityStage");
	}

	public function beforeFilter(\Cake\Event\EventInterface $event)
	{
		parent::beforeFilter($event);
		// Configure the login action to not require authentication, preventing
		// the infinite redirect loop issue
		$this->Authentication->addUnauthenticatedActions(['login', 'signup']);
	}

	public function index()
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$this->Authorization->skipAuthorization();
		// $this->loadComponent('Paginator');
		$this->paginate();

		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$role = $userSession['role'];
		$session->write('menu', 0);

		$company = $this->fetchTable('Users');

		$companies = $company
			->find()
			->where(['deleted' => 1, 'role' => 1])
			->all();
		//retreive total companies number		    
		$totalCompanies	= 	$company
			->find()
			->where(['deleted' => 1, 'role' => 1])
			->count();

		//retreive total Active Companies number
		$totalActiveCompanies	= $company->find()->where(['deleted' => 1, 'role' => 1, 'status' => 1])->count();

		$totalInactiveCompanies	= $company->find()->where(['deleted' => 1, 'role' => 1, 'status' => 0])->count();

		$this->set('title', 'PMA');

		$this->set(compact('companies', 'totalCompanies', 'totalActiveCompanies', 'totalInactiveCompanies'));
	}

	public function add()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();


		if ($this->request->is('post')) {
			$this->Users = $this->fetchTable('Users');
			$company = $this->Users->newEmptyEntity();
			$company = $this->Users->patchEntity($company, $this->request->getData());
			$company->name = $this->request->getData('contact_person_name');
			$company->role = 1;
			$company->status = 1;
			// $company->password = Security::hash('password');
			if ($this->Users->save($company)) {
				echo "true";
			}
		}
	}
	//change status
	public function updateStatus($id, $status)
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		if ($this->request->is('ajax')) {
			$company = $this->fetchTable('Users');
			$query = $company->query();
			$query->update()
				->set(['status' => $status])
				->where(['id' => $id])
				->execute();
			return $this->redirect(['controller' => 'Companies', 'action' => 'index']);
		}
	}


	public function updateActive($id, $status)
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();


		if ($this->request->is('ajax')) {

			$projectsTable = $this->fetchTable('Projects');

			$projectsTable->updateQuery()
				->set(['active' => $status])
				->where(['id' => $id])
				->execute();

			echo 1;
			die;
		}

		echo 0;
		die;
	}


	// public function updateActive($id, $status)
	// {
	// 	// echo $id;
	// 	// echo $status;
	// 	// die;
	// 	$this->autoRender = false;
	// 	$this->Authorization->skipAuthorization();
	// 	if ($this->request->is('ajax')) {
	// 		$company = $this->fetchTable('Projects');
	// 		$query = $company->query();
	// 		$query->update()
	// 			->set(['active' => $status])
	// 			->where(['id' => $id]);

	// 		if ($query->execute())
	// 			echo 1;
	// 		else
	// 			echo 0;

	// 		// if ($page == 'list')
	// 		// 	return $this->redirect(['controller' => 'Companies', 'action' => 'listProject']);
	// 		// else
	// 		// 	return $this->redirect(['controller' => 'Companies', 'action' => 'activeProject']);
	// 	}
	// }
	// delete data 
	public function delete($id)
	{
		$this->Authorization->skipAuthorization();
		$company = $this->fetchTable('Users');
		$query = $company->query();
		$query->update()
			->set(['deleted' => 0])
			->where(['id' => $id])
			->execute();
		return $this->redirect(['controller' => 'Companies', 'action' => 'index']);
	}
	public function edit($id)
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$company = $this->fetchTable('Users');
		$company = $company
			->findById($id)
			->firstOrFail();

		echo json_encode($company);
	}
	public function editData($id)
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$this->Users = $this->fetchTable('Users');
		$company = $this->Users
			->findById($id)
			->firstOrFail();

		if ($this->request->is(['post', 'put'])) {

			$company = $this->Users->patchEntity($company, $this->request->getData());
			if ($this->Users->save($company)) {
				echo "true";
			}
		}
	}

	//login as company
	public function loginVendor($id)
	{
		$this->Authorization->skipAuthorization();
		$this->Users = $this->fetchTable('Users');
		$session = new \Cake\Http\Session();

		$user = $this->Users
			->findById($id)
			->firstOrFail();
		$user_id = $user->id;
		$user_email = $user->email;
		$user_name = $user->name;
		$client_name = $user->client_name;
		$company_name = $user->company_name;
		$user_role = $user->role;
		$user_parent_id = $user->company_id;
		$contact_person_name = $user->contact_person_name;
		$role = array(4);
		$userSession = $session->read('data');
		// echo '<pre>';
		// print_r($userSession);
		// die;
		$type = $userSession['type'];

		$session_array = array('id' => $user_id, 'email' => $user_email, 'name' => $user_name, 'role' => $user_role, 'parent_id' => $user_parent_id, 'client_name' => $client_name, 'company_name' => $company_name, 'type' => $type, 'contact_person_name' => $contact_person_name, 'role_name' => $role);
		$session = new \Cake\Http\Session();
		$session->write('data', $session_array);
		$session->write('menu', 1);
		$redirect = $this->request->getQuery('redirect', [
			'controller' => 'Companies',
			'action' => 'listProject',
		]);

		return $this->redirect($redirect);
	}


	// public function addProject($id = null)
	// {

	// 	//set layout
	// 	$this->viewBuilder()->setLayout('default_new');
	// 	$this->Authorization->skipAuthorization();
	// 	$conn = ConnectionManager::get('default');
	// 	$this->Users = $this->fetchTable('Users');

	// 	$session = new \Cake\Http\Session();
	// 	$userSession = $session->read('data');
	// 	$role = $userSession['role'];
	// 	$parent_id = ($role == 1) ? $userSession['id'] : $userSession['parent_id'];


	// 	if ($this->request->is('post')) {
	// 		$this->autoRender = false;
	// 		$this->Projects = $this->fetchTable('Projects');

	// 		$projects = $this->Projects->find('all')
	// 			->select(['id'])
	// 			->where(['project_name' => $this->request->getData('project_name'), 'user_id' => $parent_id])
	// 			->toArray();

	// 		if (count($projects) > 0) {
	// 			$redirect = $this->request->getQuery('redirect', [
	// 				'controller' => 'Companies',
	// 				'action' => 'addProject',
	// 			]);

	// 			$this->Flash->error(__('This Project Already exist.'));


	// 			return $this->redirect($redirect);
	// 		}

	// 		$project = $this->Projects->newEmptyEntity();
	// 		$project = $this->Projects->patchEntity($project, $this->request->getData());
	// 		//get client id
	// 		$client = $this->Users->find('all')
	// 			->select(['id'])
	// 			->where(['client_name' => $this->request->getData('client_name'), 'company_id' => $parent_id])
	// 			->first();

	// 		$project->client_id = $client->id;

	// 		$project->user_id = $parent_id;
	// 		$date = explode('/', $this->request->getData('awarded_on'));
	// 		$awarded_on = $date[2] . '-' . $date[0] . '-' . $date[1];
	// 		$project->awarded_on = $awarded_on;

	// 		$date = explode('/', $this->request->getData('due_date'));
	// 		$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
	// 		$project->due_date = $due_date;

	// 		$resource = $this->request->getData('resources');
	// 		$resources = implode(',', $resource);
	// 		$project->resources = $resources;

	// 		$milestones = '';
	// 		if (!empty($this->request->getData('milesid'))) {
	// 			$milestone = $this->request->getData('milesid');
	// 			$milestones = implode(',', $milestone);
	// 		}
	// 		$project->milestone_id = $milestones;

	// 		$payments = '';
	// 		if (!empty($this->request->getData('ptid'))) {
	// 			$payment = $this->request->getData('ptid');
	// 			$payments = implode(',', $payment);
	// 		}
	// 		$project->payment_id = $payments;

	// 		$project->hourly_rate = $this->request->getData('hourly_rate') ? $this->request->getData('hourly_rate') : 0;

	// 		if ($this->request->getData('bill') == "on")
	// 			$project->bill = "Non Billable";
	// 		else
	// 			$project->bill = "Billable";

	// 		if ($result = $this->Projects->save($project)) {

	// 			$redirect = $this->request->getQuery('redirect', [
	// 				'controller' => 'Companies',
	// 				'action' => 'editProject',
	// 				$result->id
	// 			]);

	// 			$this->Flash->success(__('Your Project has been saved.'));

	// 			return $this->redirect($redirect);
	// 		} else {
	// 			$this->Flash->error(__('Unable to add your article.'));
	// 		}
	// 	}


	// 	if ($id) {
	// 		$query = "SELECT p.*,c.client_name FROM projects p LEFT JOIN users c ON p.client_id = c.id WHERE p.id=" . $id;
	// 		$stmtProduct = $conn->execute($query);
	// 		$list = $stmtProduct->fetchAll('assoc');

	// 		$projects = array();
	// 		$miles = array();
	// 		$payments = array();
	// 		$reslist = $resourceList = array();
	// 		foreach ($list as $l) {
	// 			$p['id'] = $l['id'];
	// 			$p['project_name'] = $l['project_name'];
	// 			$p['client']    = $l['client_name'];
	// 			$p['award']   = date('m/d/Y', strtotime($l['awarded_on']));
	// 			$p['due_date'] = date('m/d/Y', strtotime($l['due_date']));
	// 			$p['type'] = $l['project_type'];
	// 			$p['amount'] = $l['amount'];
	// 			$p['status'] = $l['status'];
	// 			$p['mlid'] = $l['milestone_id'];
	// 			$p['payid'] = $l['payment_id'];
	// 			$p['project_manager_id'] = $l['project_manager_id'];
	// 			$p['tech_lead_id'] = $l['tech_lead_id'];
	// 			$p['bd_id'] = $l['bd_id'];
	// 			$p['resource'] = $l['resources'];
	// 			$p['source'] = $l['source'];

	// 			$projects[] = $p;
	// 			$mileid = $l['milestone_id'];
	// 			$paymentid = $l['payment_id'];
	// 			$resourceid = $l['resources'];
	// 		}
	// 		if ($mileid) {
	// 			$query = "SELECT p.* FROM project_milestones p WHERE p.id IN (" . $mileid . ")";
	// 			$stmtProduct = $conn->execute($query);
	// 			$list = $stmtProduct->fetchAll('assoc');

	// 			foreach ($list as $l) {
	// 				$query = "SELECT p.* FROM project_tasks p WHERE p.milestone_id =" . $l['id'];
	// 				$stmtProduct = $conn->execute($query);
	// 				$tlist = $stmtProduct->fetchAll('assoc');
	// 				$p['task_list'] = array();
	// 				if (count($tlist) > 0) {
	// 					foreach ($tlist as $tl) {
	// 						$t['id'] = $tl['id'];
	// 						$t['task'] = $tl['task'];
	// 						$t['due_date'] = ($tl['due_date'] != '1800-01-01') ? date('d F Y', strtotime($tl['due_date'])) : '';
	// 						$t['status'] = $tl['status'];
	// 						$p['task_list'][] = $t;
	// 					}
	// 				}

	// 				$p['id'] = $l['id'];
	// 				$p['title'] = $l['title'];
	// 				$p['due_date'] = date('d F Y', strtotime($l['due_date']));
	// 				$p['amount'] = $l['amount'];
	// 				$p['status'] = $l['status'];

	// 				$miles[] = $p;
	// 			}
	// 		}

	// 		if ($paymentid) {
	// 			$query = "SELECT p.* FROM project_payments p WHERE p.id IN (" . $paymentid . ")";
	// 			$stmtProduct = $conn->execute($query);
	// 			$list = $stmtProduct->fetchAll('assoc');

	// 			foreach ($list as $l) {
	// 				$p['id'] = $l['id'];
	// 				$p['description'] = $l['description'];
	// 				$p['payment_date'] = date('d F Y', strtotime($l['payment_date']));
	// 				$p['receive_amt'] = $l['receive_amt'];
	// 				$p['status'] = $l['status'];

	// 				$payments[] = $p;
	// 			}
	// 		}

	// 		if ($resourceid) {
	// 			$query = "SELECT id,name FROM users WHERE id IN (" . $resourceid . ")";
	// 			$stmtProduct = $conn->execute($query);
	// 			$list = $stmtProduct->fetchAll('assoc');
	// 			foreach ($list as $l) {
	// 				$n = explode(' ', $l['name']);

	// 				$t['name'] = $n[0];
	// 				$reslist[] = $t;
	// 			}

	// 			if (count($miles) > 0) {
	// 				foreach ($miles as $m) {
	// 					$abc['id'] = $m['id'];
	// 					$abc['title'] = $m['title'];
	// 					$abc['res'] = array();
	// 					foreach ($list as $l) {
	// 						$n = explode(' ', $l['name']);

	// 						$t['name'] = $n[0];
	// 						$t['id'] = $l['id'];
	// 						$t['time'] = $t['worked'] = 0;

	// 						$query = "SELECT time_slot FROM project_allocations WHERE milestone_id=" . $m['id'] . " AND resource_id=" . $l['id'];
	// 						$stmtProduct = $conn->execute($query);
	// 						$mlist = $stmtProduct->fetchAll('assoc');
	// 						if (count($mlist) > 0)
	// 							$t['time'] = $mlist[0]['time_slot'];

	// 						$query = "SELECT IFNULL(SUM(time_used),0) as worked FROM user_timesheets WHERE milestone_id=" . $m['id'] . " AND resource_id=" . $l['id'];
	// 						$stmtProduct = $conn->execute($query);
	// 						$mlist = $stmtProduct->fetchAll('assoc');
	// 						if (count($mlist) > 0)
	// 							$t['worked'] = $mlist[0]['worked'];
	// 						$abc['res'][] = $t;
	// 					}

	// 					$resourceList[] = $abc;
	// 				}
	// 			}
	// 		}

	// 		$manager = $this->Users->find('all')
	// 			->where(['role' => 3, 'FIND_IN_SET(\'4\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
	// 		$techlead = $this->Users->find('all')
	// 			->where(['role' => 3, 'FIND_IN_SET(\'5\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();

	// 		$bdteam = $this->Users->find('all')
	// 			->where(['role' => 3, 'FIND_IN_SET(\'6\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();

	// 		$resources = $this->Users->find()
	// 			->where(function (QueryExpression $exp) use ($parent_id) {
	// 				$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
	// 					->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
	// 				return $exp
	// 					->add($orConditions)
	// 					->eq('deleted', 1)
	// 					->eq('role', 3)
	// 					->eq("status", 1)
	// 					->eq('company_id', $parent_id);
	// 			})->order(["name" => "ASC"])
	// 			->toArray();

	// 		$this->set(compact('id', 'projects', 'miles', 'payments', 'reslist', 'manager', 'techlead', 'bdteam', 'resources', 'resourceList'));
	// 	}

	// 	$manager = $this->Users->find('all')
	// 		->where(['role' => 3, 'FIND_IN_SET(\'4\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
	// 	$techlead = $this->Users->find('all')
	// 		->where(['role' => 3, 'FIND_IN_SET(\'5\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();

	// 	$bdteam = $this->Users->find('all')
	// 		->where(['role' => 3, 'FIND_IN_SET(\'6\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();

	// 	$resources = $this->Users->find()
	// 		->where(function (QueryExpression $exp) use ($parent_id) {
	// 			$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
	// 				->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
	// 			return $exp
	// 				->add($orConditions)
	// 				->eq('deleted', 1)
	// 				->eq('role', 3)
	// 				->eq("status", 1)
	// 				->eq('company_id', $parent_id);
	// 		})->order(["name" => "ASC"])
	// 		->toArray();
	// 	// $this->Users->find('all')
	// 	//                 ->where(['Users.parent_id' => $parent_id
	// 	//                 	,'role'=>3,'FIND_IN_SET(\'7\',Users.role_name) !='=>0,'Users.deleted'=>1])->toArray();                                                               

	// 	$this->set(compact('manager', 'techlead', 'bdteam', 'resources'));
	// }

	public function addProject($id = null)
	{
		// Set layout
		$this->viewBuilder()->setLayout('default_new');
		$this->Authorization->skipAuthorization();
		$conn = ConnectionManager::get('default');
		$this->Users = $this->fetchTable('Users');

		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$role = $userSession['role'];
		$parent_id = ($role == 1) ? $userSession['id'] : $userSession['parent_id'];

		if ($this->request->is('post')) {
			$this->autoRender = false;
			$this->Projects = $this->fetchTable('Projects');

			$projects = $this->Projects->find('all')
				->select(['id'])
				->where(['project_name' => $this->request->getData('project_name'), 'user_id' => $parent_id])
				->toArray();

			if (count($projects) > 0) {
				$redirect = $this->request->getQuery('redirect', [
					'controller' => 'Companies',
					'action' => 'addProject',
				]);

				$this->Flash->error(__('This Project Already exist.'));
				return $this->redirect($redirect);
			}

			$project = $this->Projects->newEmptyEntity();
			$project = $this->Projects->patchEntity($project, $this->request->getData());

			// Get client id
			$client = $this->Users->find('all')
				->select(['id'])
				->where(['client_name' => $this->request->getData('client_name'), 'company_id' => $parent_id])
				->first();

			$project->client_id = $client->id ?? null;
			$project->user_id = $parent_id;

			// Format awarded_on and due_date
			$date = explode('/', $this->request->getData('awarded_on'));
			$awarded_on = $date[2] . '-' . $date[0] . '-' . $date[1];
			$project->awarded_on = $awarded_on;

			$date = explode('/', $this->request->getData('due_date'));
			$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
			$project->due_date = $due_date;

			// Handle resources array
			$resource = $this->request->getData('resources');
			// $resources = implode(',', $resource);
			if (empty($resource)) {
				$resources = '';
			} elseif (is_array($resource)) {
				$resources = implode(',', $resource);
			} else {
				$resources = $resource;
			}
			$project->resources = $resources;

			// Handle milestones
			$milestones = '';
			if (!empty($this->request->getData('milesid'))) {
				$milestone = $this->request->getData('milesid');
				$milestones = implode(',', $milestone);
			}
			$project->milestone_id = $milestones;

			// Handle payments
			$payments = '';
			if (!empty($this->request->getData('ptid'))) {
				$payment = $this->request->getData('ptid');
				$payments = implode(',', $payment);
			}
			$project->payment_id = $payments;

			// ✅ FIX: Clean and convert numeric values
			$amount = $this->request->getData('amount');
			$amount = str_replace(',', '', $amount); // remove commas
			$project->amount = (float)$amount;

			$hourly_rate = $this->request->getData('hourly_rate');
			$hourly_rate = str_replace(',', '', $hourly_rate); // remove commas
			$project->hourly_rate = $hourly_rate ? (float)$hourly_rate : 0;

			// Handle bill type
			if ($this->request->getData('bill') == "on")
				$project->bill = "Non Billable";
			else
				$project->bill = "Billable";

			// Save project
			if ($result = $this->Projects->save($project)) {
				$redirect = $this->request->getQuery('redirect', [
					'controller' => 'Companies',
					'action' => 'editProject',
					$result->id
				]);

				$this->Flash->success(__('Your Project has been saved.'));
				return $this->redirect($redirect);
			} else {
				$this->Flash->error(__('Unable to add your article.'));
			}
		}

		// ======== REMAINING CODE BELOW (no changes) ========

		if ($id) {
			$query = "SELECT p.*,c.client_name FROM projects p LEFT JOIN users c ON p.client_id = c.id WHERE p.id=" . $id;
			$stmtProduct = $conn->execute($query);
			$list = $stmtProduct->fetchAll('assoc');

			$projects = array();
			$miles = array();
			$payments = array();
			$reslist = $resourceList = array();
			foreach ($list as $l) {
				$p['id'] = $l['id'];
				$p['project_name'] = $l['project_name'];
				$p['client'] = $l['client_name'];
				$p['award'] = date('m/d/Y', strtotime($l['awarded_on']));
				$p['due_date'] = date('m/d/Y', strtotime($l['due_date']));
				$p['type'] = $l['project_type'];
				$p['amount'] = $l['amount'];
				$p['status'] = $l['status'];
				$p['mlid'] = $l['milestone_id'];
				$p['payid'] = $l['payment_id'];
				$p['project_manager_id'] = $l['project_manager_id'];
				$p['tech_lead_id'] = $l['tech_lead_id'];
				$p['bd_id'] = $l['bd_id'];
				$p['resource'] = $l['resources'];
				$p['source'] = $l['source'];

				$projects[] = $p;
				$mileid = $l['milestone_id'];
				$paymentid = $l['payment_id'];
				$resourceid = $l['resources'];
			}

			if ($mileid) {
				$query = "SELECT p.* FROM project_milestones p WHERE p.id IN (" . $mileid . ")";
				$stmtProduct = $conn->execute($query);
				$list = $stmtProduct->fetchAll('assoc');

				foreach ($list as $l) {
					$query = "SELECT p.* FROM project_tasks p WHERE p.milestone_id =" . $l['id'];
					$stmtProduct = $conn->execute($query);
					$tlist = $stmtProduct->fetchAll('assoc');
					$p['task_list'] = array();
					if (count($tlist) > 0) {
						foreach ($tlist as $tl) {
							$t['id'] = $tl['id'];
							$t['task'] = $tl['task'];
							$t['due_date'] = ($tl['due_date'] != '1800-01-01') ? date('d F Y', strtotime($tl['due_date'])) : '';
							$t['status'] = $tl['status'];
							$p['task_list'][] = $t;
						}
					}

					$p['id'] = $l['id'];
					$p['title'] = $l['title'];
					$p['due_date'] = date('d F Y', strtotime($l['due_date']));
					$p['amount'] = $l['amount'];
					$p['status'] = $l['status'];
					$miles[] = $p;
				}
			}

			if ($paymentid) {
				$query = "SELECT p.* FROM project_payments p WHERE p.id IN (" . $paymentid . ")";
				$stmtProduct = $conn->execute($query);
				$list = $stmtProduct->fetchAll('assoc');

				foreach ($list as $l) {
					$p['id'] = $l['id'];
					$p['description'] = $l['description'];
					$p['payment_date'] = date('d F Y', strtotime($l['payment_date']));
					$p['receive_amt'] = $l['receive_amt'];
					$p['status'] = $l['status'];
					$payments[] = $p;
				}
			}

			if ($resourceid) {
				$query = "SELECT id,name FROM users WHERE id IN (" . $resourceid . ")";
				$stmtProduct = $conn->execute($query);
				$list = $stmtProduct->fetchAll('assoc');
				foreach ($list as $l) {
					$n = explode(' ', $l['name']);
					$t['name'] = $n[0];
					$reslist[] = $t;
				}

				if (count($miles) > 0) {
					foreach ($miles as $m) {
						$abc['id'] = $m['id'];
						$abc['title'] = $m['title'];
						$abc['res'] = array();
						foreach ($list as $l) {
							$n = explode(' ', $l['name']);
							$t['name'] = $n[0];
							$t['id'] = $l['id'];
							$t['time'] = $t['worked'] = 0;

							$query = "SELECT time_slot FROM project_allocations WHERE milestone_id=" . $m['id'] . " AND resource_id=" . $l['id'];
							$stmtProduct = $conn->execute($query);
							$mlist = $stmtProduct->fetchAll('assoc');
							if (count($mlist) > 0)
								$t['time'] = $mlist[0]['time_slot'];

							$query = "SELECT IFNULL(SUM(time_used),0) as worked FROM user_timesheets WHERE milestone_id=" . $m['id'] . " AND resource_id=" . $l['id'];
							$stmtProduct = $conn->execute($query);
							$mlist = $stmtProduct->fetchAll('assoc');
							if (count($mlist) > 0)
								$t['worked'] = $mlist[0]['worked'];
							$abc['res'][] = $t;
						}

						$resourceList[] = $abc;
					}
				}
			}

			$manager = $this->Users->find('all')
				->where(['role' => 3, 'FIND_IN_SET(\'4\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
			$techlead = $this->Users->find('all')
				->where(['role' => 3, 'FIND_IN_SET(\'5\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
			$bdteam = $this->Users->find('all')
				->where(['role' => 3, 'FIND_IN_SET(\'6\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
			$resources = $this->Users->find()
				->where(function (QueryExpression $exp) use ($parent_id) {
					$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
						->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
					return $exp
						->add($orConditions)
						->eq('deleted', 1)
						->eq('role', 3)
						->eq("status", 1)
						->eq('company_id', $parent_id);
				})->order(["name" => "ASC"])
				->toArray();

			$this->set(compact('id', 'projects', 'miles', 'payments', 'reslist', 'manager', 'techlead', 'bdteam', 'resources', 'resourceList'));
		}

		$manager = $this->Users->find('all')
			->where(['role' => 3, 'FIND_IN_SET(\'4\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
		$techlead = $this->Users->find('all')
			->where(['role' => 3, 'FIND_IN_SET(\'5\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
		$bdteam = $this->Users->find('all')
			->where(['role' => 3, 'FIND_IN_SET(\'6\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
		$resources = $this->Users->find()
			->where(function (QueryExpression $exp) use ($parent_id) {
				$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
					->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
				return $exp
					->add($orConditions)
					->eq('deleted', 1)
					->eq('role', 3)
					->eq("status", 1)
					->eq('company_id', $parent_id);
			})->order(["name" => "ASC"])
			->toArray();

		$this->set(compact('manager', 'techlead', 'bdteam', 'resources'));
	}



	public function myProject($projectType = null)
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$this->Projects = $this->fetchTable('Projects');
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		$role = $userSession['role'];
		$parent_id = $userSession['parent_id'];

		$this->request->getSession()->write('managerId', $user_id);
		$this->request->getSession()->write('page', 'myproject');

		$parent_id = $userSession['parent_id'];
		// $user_id = $this->request->getAttribute('identity')->getIdentifier();

		if ($projectType == 'project-manager') {

			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.status != 'Completed' AND (p.project_manager_id=$user_id)";
		} else if ($projectType == "bde") {

			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.status != 'Completed' AND (p.bd_id=$user_id)";
		} else if ($projectType == "tech-lead") {

			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.status != 'Completed' AND (p.tech_lead_id=$user_id)";
		} else if ($projectType == "resource") {

			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.status != 'Completed' AND (FIND_IN_SET(" . $user_id . ",resources)!=0)";
		} else {

			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.status != 'Completed' AND (p.project_manager_id=" . $user_id . " OR p.tech_lead_id=" . $user_id . " OR p.bd_id=" . $user_id . " OR FIND_IN_SET(" . $user_id . ",resources)!=0)";
		}
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');

		// echo '<pre>';
		// print_r($list);
		// die;

		$projects = array();
		$total = $complete = $active = 0;
		foreach ($list as $l) {
			//calculate total amount from milestone
			$amount = $overdue = $due = 0;
			if ($l['milestone_id']) {
				$query = "SELECT p.amount FROM project_milestones p WHERE p.project_id =" . $l['id'] . " and deleted = 0";
				$stmtProduct = $conn->execute($query);
				$mlist = $stmtProduct->fetchAll('assoc');

				foreach ($mlist as $m) {
					$amount += $m['amount'];
				}

				//count overdue and due mile 
				$query = "SELECT SUM(CASE when (due_date < '" . date('Y-m-d') . "' AND status != 'Completed') then 1 else 0 END) as overdue,SUM(CASE when ((due_date >= '" . date('Y-m-d') . "' AND due_date <= '" . date('Y-m-d', strtotime('+15 days')) . "') AND status != 'Completed') then 1 else 0 END) as due FROM project_milestones p WHERE p.project_id =" . $l['id'] . " and deleted = 0";
				$stmtProduct = $conn->execute($query);
				$mlist = $stmtProduct->fetchAll('assoc');
				$overdue = $mlist[0]['overdue'];
				$due = $mlist[0]['due'];
			}
			//calculate paid amount from payment history
			$paid = 0;
			if ($l['payment_id']) {
				$query = "SELECT p.receive_amt FROM project_payments p WHERE p.project_id =" . $l['id'] . " AND status='Paid'";
				$stmtProduct = $conn->execute($query);
				$plist = $stmtProduct->fetchAll('assoc');

				foreach ($plist as $p) {
					$paid += $p['receive_amt'];
				}
			}

			// $pm_amount = "SELECT sum(project_milestones.amount) as t_amount,projects.id as projId,projects.project_name FROM `project_milestones` LEFT JOIN projects ON projects.id=project_milestones.project_id WHERE projects.id=". $l['id'];
			// $stmtProduct = $conn->execute($pm_amount);
			// $pm_amount = $stmtProduct->fetchAll('assoc');

			// new function

			$pm_amount = "SELECT 
							SUM(project_milestones.amount) AS t_amount,
							projects.id AS projId,
							projects.project_name 
						FROM project_milestones 
						LEFT JOIN projects 
							ON projects.id = project_milestones.project_id 
						WHERE projects.id = :id 
							AND project_milestones.deleted = 0";

			$stmtProduct = $conn->execute($pm_amount, [
				'id' => $l['id']
			]);

			$pm_amount = $stmtProduct->fetchAll('assoc');
			// dd($pm_amount[0]['t_amount']);

			$actual_hours = "SELECT sum(user_timesheets.time_used) as time_used FROM `user_timesheets` LEFT JOIN project_milestones ON project_milestones.id=user_timesheets.milestone_id LEFT JOIN projects ON projects.id=project_milestones.project_id WHERE projects.id=".$l['id'];
			$stmtProduct = $conn->execute($actual_hours);
			$actual_hours = $stmtProduct->fetchAll('assoc');
			
			$allocated_hours = "SELECT sum(project_allocations.time_slot) as time_slot FROM `project_allocations` LEFT JOIN project_milestones ON project_allocations.milestone_id=project_milestones.id LEFT JOIN projects ON project_milestones.project_id=projects.id WHERE projects.id=".$l['id'];
			$stmtProduct = $conn->execute($allocated_hours);
			$allocated_hours = $stmtProduct->fetchAll('assoc');

			$p['id'] = $l['id'];
			$p['project_name'] = $l['project_name'];
			$p['client_id']    = $l['client_id'];
			$p['client']    = $l['client_name'];
			$p['project_manager']    = $l['project_manager'];
			$p['award']   = date('d F Y', strtotime($l['awarded_on']));
			$p['due_date'] = date('d-m-Y', strtotime($l['due_date']));
			$p['type'] = $l['project_type'];
			$p['hourly_rate'] = $l['hourly_rate'];
			$p['amount'] = $amount;
			$p['paid'] = $paid;
			$p['status'] = $l['status'];
			$p['source'] = $l['source'];
			$p['active'] = $l['active'];
			$p['overdue'] = $overdue;
			$p['due'] = $due;
			$p['pm_amount']=$pm_amount[0]['t_amount'];
			$p['actual_hours']=$actual_hours[0]['time_used'];
			$p['allocated_hours']=$allocated_hours[0]['time_slot'];
			if($p['hourly_rate']==0){
				$p['budget']='Na';
			} else {
				$p['budget'] = ($pm_amount[0]['t_amount']/$p['hourly_rate']);
			}
			

			$projects[] = $p;

			$total++;
			if ($l['status'] == 'Pending')
				$active++;
			else
				$complete++;
		}

		$my = count($projects);

		$role = $userSession['role'];
		if ($role == 1) {
			$uwh = " AND u.company_id=" . $user_id;
			$wh = " AND p.user_id=" . $user_id;
		} elseif ($role == 3) {
			$uwh = " AND u.company_id=" . $parent_id;
			$wh = " AND p.user_id=" . $parent_id;
		} else
			$wh = "";

		//count total project
		$query = "SELECT p.id FROM projects p WHERE p.deleted=1" . $wh;
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');
		$count = count($list);

		//count active project
		$query = "SELECT p.id FROM projects p WHERE p.deleted=1 AND p.active=1" . $wh;
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');
		$active = count($list);
		// dd($projects);
		$this->set(compact('projects', 'total', 'active', 'complete', 'my', 'count', 'projectType'));
	}

	public function listProject($manager_id = null)
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$this->Projects = $this->fetchTable('Projects');
		$session = new \Cake\Http\Session();
		$this->request->getSession()->write('managerId', $manager_id);
		$this->request->getSession()->write('page', 'listproject');

		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		$role = $userSession['role'];
		$parent_id = $userSession['parent_id'];

		if ($manager_id == "all-project") $manager_id = null;
		else if ($manager_id == null) $manager_id = $user_id;
		// echo $manager_id;
		// print_r($this->request->getQuery());
		// die;

		//check user level that it can see this page or not
		if (($userSession['role'] == 3) && (!array_intersect($userSession['role_name'], array(10))))
			return $this->redirect(['controller' => 'Companies', 'action' => 'myProject']);

		if ($role == 1) {
			$wh = " AND p.user_id=" . $user_id;
			$uwh = " AND u.company_id=" . $user_id;
		} elseif ($role == 3) {
			$wh = " AND p.user_id=" . $parent_id;
			$uwh = " AND u.company_id=" . $parent_id;
		} else
			$wh = "";

		// $user_id = $this->request->getAttribute('identity')->getIdentifier();
		if ($manager_id) {
			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.project_manager_id =" . $manager_id;
		} else {
			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1" . $wh;
		}
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');

		$projects = array();
		$total = $complete = $active =  $totalPaid = $totalAmount = 0;

		foreach ($list as $l) {
			//calculate total amount from milestone
			$amount = $overdue = $due = 0;
			if ($l['milestone_id']) {
				$query =  "SELECT p.amount FROM project_milestones p WHERE p.project_id =" . $l['id'] . " and deleted = 0";
				$stmtProduct = $conn->execute($query);
				$mlist = $stmtProduct->fetchAll('assoc');

				foreach ($mlist as $m) {
					$amount += $m['amount'];
				}

				//count overdue and due mile 
				$query = "SELECT SUM(CASE when (due_date < '" . date('Y-m-d') . "' AND status != 'Completed') then 1 else 0 END) as overdue,SUM(CASE when ((due_date >= '" . date('Y-m-d') . "' AND due_date <= '" . date('Y-m-d', strtotime('+15 days')) . "') AND status != 'Completed') then 1 else 0 END) as due FROM project_milestones p WHERE p.project_id =" . $l['id'];
				$stmtProduct = $conn->execute($query);
				$mlist = $stmtProduct->fetchAll('assoc');
				$overdue = $mlist[0]['overdue'];
				$due = $mlist[0]['due'];
			}
			//calculate paid amount from payment history
			$paid = 0;
			if ($l['payment_id']) {
				$query = "SELECT p.receive_amt FROM project_payments p WHERE p.project_id =" . $l['id'] . " AND status='Paid'";
				$stmtProduct = $conn->execute($query);
				$plist = $stmtProduct->fetchAll('assoc');

				foreach ($plist as $p) {
					$paid += $p['receive_amt'];
				}
			}

			$p['id'] = $l['id'];
			$p['project_name'] = $l['project_name'];
			$p['client_id']    = $l['client_id'];
			$p['client']    = $l['client_name'];
			$p['project_manager']    = $l['project_manager'];
			$p['award']   = date('d M Y', strtotime($l['awarded_on']));
			$p['due_date'] = date('d M Y', strtotime($l['due_date']));
			$p['type'] = $l['project_type'];
			$p['amount'] = $amount;
			$p['paid'] = $paid;
			$p['status'] = $l['status'];
			$p['active'] = $l['active'];
			$p['overdue'] = $overdue;
			$p['due'] = $due;
			$totalAmount += $p['amount'];
			$totalPaid += $p['paid'];


			$projects[] = $p;


			if ($l['status'] == 'Pending')
				$active++;
			else
				$complete++;
		}

		$count = count($projects);

		//count active project
		$query = "SELECT p.id FROM projects p WHERE p.deleted=1 AND p.active=1" . $wh;
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');
		$active = count($list);

		$my = 0;
		if ($role == 3) {
			$query = "SELECT p.id FROM projects p WHERE p.deleted=1 AND p.status != 'Completed' AND (p.project_manager_id=" . $user_id . " OR p.tech_lead_id=" . $user_id . " OR p.bd_id=" . $user_id . " OR FIND_IN_SET(" . $user_id . ",resources)!=0)";
			$stmtProduct = $conn->execute($query);
			$list = $stmtProduct->fetchAll('assoc');
			$my = count($list);
		}

		// $queryManager = "SELECT u.id,u.name,u.role_name from users u where u.deleted = 1 and u.role = 3  and find_in_set('4',u.role_name)!=0" . $uwh;
		$queryManager = "
			SELECT DISTINCT u.id, u.name, u.role_name
			FROM users u
			INNER JOIN projects p ON p.project_manager_id = u.id AND p.deleted = 1
			WHERE u.deleted = 1 
			AND u.role = 3  
			AND find_in_set('4', u.role_name) != 0
			" . $uwh;

		$stmtManager = $conn->execute($queryManager);
		$projectManagers = $stmtManager->fetchAll('assoc');


		// echo "<pre>";
		// print_r($projectManagers);
		// die;
		$this->set(compact('projects', 'active', 'complete', 'my', 'count', 'active', 'projectManagers', 'totalPaid', 'totalAmount', 'manager_id', 'user_id'));
	}


	public function activeProject($manager_id = null)
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$this->Projects = $this->fetchTable('Projects');

		$this->request->getSession()->write('managerId', $manager_id);
		$this->request->getSession()->write('page', 'active');

		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		$role = $userSession['role'];
		$parent_id = $userSession['parent_id'];

		if ($manager_id == "all-project") $manager_id = null;
		else if ($manager_id == null) $manager_id = $user_id;
		// echo $manager_id;
		// print_r($this->request->getQuery());
		// die;

		if ($role == 1) {
			$wh = " AND p.user_id=" . $user_id;
			$uwh = " AND u.company_id=" . $user_id;
		} elseif ($role == 3) {
			$wh = " AND p.user_id=" . $parent_id;
			$uwh = " AND u.company_id=" . $parent_id;
		} else
			$wh = "";

		// $user_id = $this->request->getAttribute('identity')->getIdentifier();
		if ($manager_id) {
			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.active=1 AND p.project_manager_id =" . $manager_id;
		} else {
			$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.active=1" . $wh;
		}
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');

		$projects = array();
		$total = $complete = $active = $totalPaid = $totalAmount = 0;
		foreach ($list as $l) {
			//calculate total amount from milestone
			$amount = $overdue = $due = 0;
			if ($l['milestone_id']) {
				$query = "SELECT p.amount FROM project_milestones p WHERE p.project_id =" . $l['id'] . " and deleted = 0";
				$stmtProduct = $conn->execute($query);
				$mlist = $stmtProduct->fetchAll('assoc');

				foreach ($mlist as $m) {
					$amount += $m['amount'];
				}

				//count overdue and due mile 
				$query = "SELECT SUM(CASE when (due_date < '" . date('Y-m-d') . "' AND status != 'Completed') then 1 else 0 END) as overdue,SUM(CASE when ((due_date >= '" . date('Y-m-d') . "' AND due_date <= '" . date('Y-m-d', strtotime('+15 days')) . "') AND status != 'Completed') then 1 else 0 END) as due FROM project_milestones p WHERE p.project_id =" . $l['id'] . " and deleted = 0";
				$stmtProduct = $conn->execute($query);
				$mlist = $stmtProduct->fetchAll('assoc');
				$overdue = $mlist[0]['overdue'];
				$due = $mlist[0]['due'];
			}
			//calculate paid amount from payment history
			$paid = 0;
			if ($l['payment_id']) {
				$query = "SELECT p.receive_amt FROM project_payments p WHERE p.project_id =" . $l['id'] . " AND status='Paid'";
				$stmtProduct = $conn->execute($query);
				$plist = $stmtProduct->fetchAll('assoc');

				foreach ($plist as $p) {
					$paid += $p['receive_amt'];
				}
			}

			$p['id'] = $l['id'];
			$p['project_name'] = $l['project_name'];
			$p['client_id']    = $l['client_id'];
			$p['client']    = $l['client_name'];
			$p['project_manager']    = $l['project_manager'];
			$p['award']   = date('d F Y', strtotime($l['awarded_on']));
			$p['due_date'] = date('d F Y', strtotime($l['due_date']));
			$p['type'] = $l['project_type'];
			$p['amount'] = $amount;
			$p['paid'] = $paid;
			$p['status'] = $l['status'];
			$p['active'] = $l['active'];
			$p['overdue'] = $overdue;
			$p['due'] = $due;
			$totalAmount += $p['amount'];
			$totalPaid += $p['paid'];


			$projects[] = $p;

			$total++;
			if ($l['status'] == 'Pending')
				$active++;
			else
				$complete++;
		}

		$active = count($projects);

		//count total project
		$query = "SELECT p.id FROM projects p WHERE p.deleted=1" . $wh;
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');
		$count = count($list);

		$my = 0;
		if ($role == 3) {
			$query = "SELECT p.id FROM projects p WHERE p.deleted=1 AND p.status != 'Completed' AND (p.project_manager_id=" . $user_id . " OR p.tech_lead_id=" . $user_id . " OR p.bd_id=" . $user_id . " OR FIND_IN_SET(" . $user_id . ",resources)!=0)";
			$stmtProduct = $conn->execute($query);
			$list = $stmtProduct->fetchAll('assoc');
			$my = count($list);
		}


		// $queryManager = "SELECT u.id,u.name,u.role_name from users u where u.deleted = 1 and u.role = 3  and find_in_set('4',u.role_name)!=0" . $uwh;

		$queryManager = "
			SELECT DISTINCT u.id, u.name, u.role_name
			FROM users u
			INNER JOIN projects p ON p.project_manager_id = u.id AND p.deleted = 1
			WHERE u.deleted = 1 
			AND u.role = 3  
			AND find_in_set('4', u.role_name) != 0
			" . $uwh;
			
		$stmtManager = $conn->execute($queryManager);
		$projectManagers = $stmtManager->fetchAll('assoc');

		$this->set(compact('projects', 'total', 'active', 'complete', 'my', 'count', 'active', 'projectManagers', 'totalPaid', 'totalAmount', 'manager_id'));
	}

		public function editProject($id, $manager_id = null)
		{
			//set layout
			$this->viewBuilder()->setLayout('default_new');
			$conn = ConnectionManager::get('default');
			$this->Authorization->skipAuthorization();
			$this->Projects = $this->fetchTable('Projects');
			$this->Users = $this->fetchTable('Users');
			$this->MilestoneExtend = $this->fetchTable('MilestoneExtend');

			$mId = $this->request->getSession()->read('managerId');
			// $userId = $this->request->getSession()->read('userId');
			$page = $this->request->getSession()->read('page');

			$this->request->getSession()->delete('managerId');
			$this->request->getSession()->delete('page');

			// echo $mId;
			// echo $page;
			// die;

			$session = new \Cake\Http\Session();
			$userSession = $session->read('data');
			$role = $userSession['role'];
			$user_id = $parent_id = ($role == 1) ? $userSession['id'] : $userSession['parent_id'];

			$query = "SELECT p.*,c.client_name FROM projects p LEFT JOIN users c ON p.client_id = c.id WHERE p.deleted=1 AND p.id=" . $id;
			$stmtProduct = $conn->execute($query);
			$list = $stmtProduct->fetchAll('assoc');

			$projects = array();
			$miles = array();
			$payments = array();
			$reslist = array();
			$resourceList = array();
			foreach ($list as $l) {
				$p['id'] = $l['id'];
				$p['project_name'] = $l['project_name'];
				$p['client']    = $l['client_name'];
				$p['upwork_ref_id']    = $l['upwork_ref_id'];
				$p['award']   = date('m/d/Y', strtotime($l['awarded_on']));
				$p['due_date'] = date('m/d/Y', strtotime($l['due_date']));
				$p['type'] = $l['project_type'];
				$p['amount'] = $l['amount'];
				$p['status'] = $l['status'];
				$p['mlid'] = $l['milestone_id'];
				$p['payid'] = $l['payment_id'];
				$p['project_manager_id'] = $l['project_manager_id'];
				$p['tech_lead_id'] = $l['tech_lead_id'];
				$p['bd_id'] = $l['bd_id'];
				$p['resources'] = $l['resources'];
				$p['hourly_rate'] = $l['hourly_rate'];
				$p['bill'] = $l['bill'];
				$p['source'] = $l['source'];

				$projects[] = $p;
				$mileid = $l['milestone_id'];
				$paymentid = $l['payment_id'];
				$resourceid = $l['resources'];
			}
			if ($mileid) {
				$query = "SELECT p.* FROM project_milestones p WHERE p.project_id =" . $id . " AND deleted=0";
				$stmtProduct = $conn->execute($query);
				$list = $stmtProduct->fetchAll('assoc');

				foreach ($list as $l) {
					$query = "SELECT p.* FROM project_tasks p WHERE p.milestone_id =" . $l['id'];
					$stmtProduct = $conn->execute($query);
					$tlist = $stmtProduct->fetchAll('assoc');
					$p['task_list'] = array();
					if (count($tlist) > 0) {
						foreach ($tlist as $tl) {
							$t['id'] = $tl['id'];
							$t['task'] = $tl['task'];
							$t['due_date'] = ($tl['due_date'] != '1800-01-01') ? date('d F Y', strtotime($tl['due_date'])) : '';
							$t['status'] = $tl['status'];
							$p['task_list'][] = $t;
						}
					}

					$p['id'] = $l['id'];
					$p['title'] = $l['milestone_month_year'] ? $l['title'] . ' ' . $l['milestone_month_year'] : $l['title'];
					$p['due_date'] = date('d F Y', strtotime($l['due_date']));
					$p['amount'] = $l['amount'];
					$p['status'] = $l['status'];

					$miles[] = $p;
				}
			}

			if ($paymentid) {
				$query = "SELECT p.* FROM project_payments p WHERE p.id IN (" . $paymentid . ")";
				$stmtProduct = $conn->execute($query);
				$list = $stmtProduct->fetchAll('assoc');

				foreach ($list as $l) {
					$p['id'] = $l['id'];
					$p['description'] = $l['description'];
					$p['payment_date'] = date('d F Y', strtotime($l['payment_date']));
					$p['receive_amt'] = $l['receive_amt'];
					$p['status'] = $l['status'];

					$payments[] = $p;
				}
			}

			if ($resourceid) {
				$query = "SELECT id,name FROM users WHERE id IN (" . $resourceid . ")";
				$stmtProduct = $conn->execute($query);
				$list = $stmtProduct->fetchAll('assoc');
				foreach ($list as $l) {
					$n = explode(' ', $l['name']);

					$t['name'] = $n[0];
					$reslist[] = $t;
				}

				if (count($miles) > 0) {
					foreach ($miles as $m) {
						$abc['id'] = $m['id'];
						$abc['title'] = $m['title'];
						$abc['status'] = $m['status'];
						$abc['due_date'] = $m['due_date'];
						$abc['res'] = array();
						foreach ($list as $l) {
							$n = explode(' ', $l['name']);

							$t['name'] = $n[0];
							$t['id'] = $l['id'];
							$t['time'] = $t['worked'] = 0;

							$query = "SELECT time_slot FROM project_allocations WHERE milestone_id=" . $m['id'] . " AND resource_id=" . $l['id'];
							$stmtProduct = $conn->execute($query);
							$mlist = $stmtProduct->fetchAll('assoc');
							if (count($mlist) > 0)
								$t['time'] = $mlist[0]['time_slot'];

							$query = "SELECT IFNULL(SUM(time_used),0) as worked FROM user_timesheets WHERE milestone_id=" . $m['id'] . " AND resource_id=" . $l['id'];
							$stmtProduct = $conn->execute($query);
							$mlist = $stmtProduct->fetchAll('assoc');
							if (count($mlist) > 0)
								$t['worked'] = $mlist[0]['worked'];
							$abc['res'][] = $t;
						}

						$resourceList[] = $abc;
					}
				}
			}

			if ($this->request->is('post')) {

				// echo "<pre>";
				// print_r($this->request->getData());
				// die;
				$this->autoRender = false;
				$this->Projects = $this->fetchTable('Projects');
				$this->Users = $this->fetchTable('Users');
				$project_resource = $this->fetchTable('ProjectResources');

				$id = $this->request->getData('project_id');
				// $project = $this->Projects
				// 	->findById($id)
				// 	->firstOrFail();
				if (empty($id)) {
					$this->Flash->error('Invalid project ID');
					return $this->redirect($this->referer());
				}

				$project = $this->Projects->get($id, [
					'contain' => []
				]);

				$project = $this->Projects->patchEntity($project, $this->request->getData());
				//get client id
				// $client = $this->Users->find('all')
				// 	->select(['id'])
				// 	->where(['client_name' => $this->request->getData('client_name'), 'company_id' => $parent_id])
				// 	->first();

				// $project->client_id = $client->id;

				$client = $this->Users->find()
					->select(['id'])
					->where([
						'client_name' => $this->request->getData('client_name'),
						'company_id' => $parent_id
					])
					->first();

				if (!empty($client)) {
					$project->client_id = $client->id;
				} else {
					$this->Flash->error('Client not found.');
					return $this->redirect($this->referer());
				}

				$date = explode('/', $this->request->getData('awarded_on'));
				$awarded_on = $date[2] . '-' . $date[0] . '-' . $date[1];
				$project->awarded_on = $awarded_on;

				$date = explode('/', $this->request->getData('due_date'));
				$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
				$project->due_date = $due_date;

				$resource = $this->request->getData('resources');
				// $resources = implode(',', $resource);
				$resources = !empty($resource) ? implode(',', $resource) : '';
				$project->resources = $resources;

				$milestones = '';
				if (!empty($this->request->getData('milesid'))) {
					$milestone = $this->request->getData('milesid');
					$milestones = implode(',', $milestone);
				}
				$project->milestone_id = $milestones;

				$payments = '';
				if (!empty($this->request->getData('ptid'))) {
					$payment = $this->request->getData('ptid');
					$payments = implode(',', $payment);
				}
				$project->payment_id = $payments;

				$project->hourly_rate = $this->request->getData('hourly_rate');

				if ($this->request->getData('bill') == "on")
					$project->bill = "Non Billable";
				else
					$project->bill = "Billable";


				if ($result = $this->Projects->save($project)) {

					$project_resource->deleteAll(['project_id' => $id]);
					$all_resource = [];
					$m = 0;
					foreach ($resource as $p_res) {
						$all_resource[$m]['project_id'] = $id;
						$all_resource[$m]['user_id'] = $p_res;
						$m++;
					}
					$entities = $project_resource->newEntities($all_resource);
					$project_resource->saveMany($entities);

					$redirect = $this->request->getQuery('redirect', [
						'controller' => 'Companies',
						'action' => 'editProject',
						$id
					]);




					return $this->redirect($redirect);
				} else {
					$this->Flash->error(__('Unable to update your project.'));
				}
			}

			// $user_id = $this->request->getAttribute('identity')->getIdentifier();
			$manager = $this->Users->find('all')
				->where(['role' => 3, 'FIND_IN_SET(\'4\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();
			$techlead = $this->Users->find('all')
				->where(['role' => 3, 'FIND_IN_SET(\'5\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();

			$bdteam = $this->Users->find('all')
				->where(['role' => 3, 'FIND_IN_SET(\'6\',Users.role_name) !=' => 0, 'Users.deleted' => 1, 'company_id' => $parent_id, "status" => 1])->order(["name" => "ASC"])->toArray();

			$resource = $this->Users->find()
				->where(function (QueryExpression $exp) use ($parent_id) {
					$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
						->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
					return $exp
						->add($orConditions)
						->eq('deleted', 1)
						->eq('role', 3)
						->eq('company_id', $parent_id)
						->eq("status", 1);
				})->order(["name" => "ASC"])->toArray();

			$projectMileDueDate = $this->ProjectMilestones->find()
				->select(['max_date' => 'MAX(due_date)'])
				->where(['project_id' => $id])
				->first()->max_date;

			$maxExtendDate = $this->MilestoneExtend->find()
				->select(['max_date' => 'MAX(extend_date)'])
				->where(['project_id' => $id])
				->first()->max_date;
			// echo $id;
			// echo '<pre>';
			// print_r($maxExtendDate);
			// die;
			$total_actual_hours=0;
			$total_allocated_hours=0;
			$actual_hours = "SELECT user_timesheets.id as uid,user_timesheets.milestone_id,user_timesheets.resource_id, user_timesheets.time_used,project_milestones.title,projects.project_name,projects.id as pid FROM `user_timesheets` LEFT JOIN project_milestones ON project_milestones.id=user_timesheets.milestone_id LEFT JOIN projects ON projects.id=project_milestones.project_id WHERE projects.id=".$id;
			$stmtProduct = $conn->execute($actual_hours);
			$actual_hours = $stmtProduct->fetchAll('assoc');
			
			foreach($actual_hours as $actual_hour){
					$total_actual_hours += $actual_hour['time_used'];
			}

			$allocated_hours = "SELECT project_allocations.time_slot FROM `project_allocations` LEFT JOIN project_milestones ON project_allocations.milestone_id=project_milestones.id LEFT JOIN projects ON project_milestones.project_id=projects.id WHERE projects.id=".$id;
			$stmtProduct = $conn->execute($allocated_hours);
			$allocated_hours = $stmtProduct->fetchAll('assoc');
			
			foreach($allocated_hours as $allocated_hour){
					$total_allocated_hours += $allocated_hour['time_slot'];
			}
			// dd($total_allocated_hours);

			// $pm_amount = "SELECT project_milestones.id,project_milestones.title,project_milestones.project_id,sum(project_milestones.amount) as t_amount,projects.id as projId,projects.project_name FROM `project_milestones` LEFT JOIN projects ON projects.id=project_milestones.project_id WHERE projects.id=". $id;

			$pm_amount = "SELECT project_milestones.id,project_milestones.title,project_milestones.project_id,sum(project_milestones.amount) as t_amount,projects.id as projId,projects.project_name FROM `project_milestones` LEFT JOIN projects ON projects.id=project_milestones.project_id WHERE projects.id=". $id. " GROUP BY project_milestones.id";

			// echo "<pre>";
			// print_r($pm_amount);
			// die();

			$stmtProduct = $conn->execute($pm_amount);
			$pm_amount = $stmtProduct->fetchAll('assoc');
			$total_pm_amount = 0;

			if (!empty($pm_amount)) {
				$total_pm_amount = $pm_amount[0]['t_amount'];
			}
			// $total_pm_amount=$pm_amount[0]['t_amount'];
				// dd($pm_amount[0]['t_amount']);
				// dd($total_pm_amount);

			$this->set(compact('projects', 'miles', 'payments', 'reslist', 'manager', 'techlead', 'bdteam', 'resource', 'resourceList', 'mId', 'page', 'id', 'projectMileDueDate', 'maxExtendDate','total_actual_hours','total_allocated_hours','total_pm_amount'));
		}


	public function deleteProject($id)
	{
		$this->Authorization->skipAuthorization();
		$conn = ConnectionManager::get('default');
		$this->Projects = $this->fetchTable('Projects');

		// echo $id;
		// die;
		// $query =  $this->Projects->query();
		// $query->update()
		// 	->set(['deleted' => 0])
		// 	->where(['id' => $id]);
		$query = $this->Projects->updateQuery();

		$query
			->set(['deleted' => 0])
			->where(['id' => $id]);

		if ($query->execute())
			echo 1;
		else
			echo 0;
		die;
		// return $this->redirect(['controller' => 'Companies', 'action' => 'listProject']);
	}

	public function milestonelog($project_id, $milestone_id, $user_id, $action_type, $data_got = null)
	{

		$roleArray_data = [];

		$roleArray_data["project_id"] = $project_id;
		$roleArray_data["project_milestone_id"] = $milestone_id;
		$roleArray_data["action_performed"] = "$action_type";
		$roleArray_data["user_id"] = $user_id;

		$milestone_logs_table = $this->ProjectMilestonesLogs->newEmptyEntity();

		// $milestone_logs_table->project_id = $project_id;
		// $milestone_logs_table->project_milestone_id = $milestone_id;
		// $milestone_logs_table->user_id = $user_id;
		// $milestone_logs_table->action_performed = $action_type;

		if ($data_got && count($data_got) > 0) {

			if (isset($data_got["old_price"])) {

				$roleArray_data["old_price"] = $data_got["old_price"];
				$roleArray_data["is_price_changed"] = $data_got["is_price_changed"];

				// $milestone_logs_table->old_price = $data_got["old_price"];
				// $milestone_logs_table->is_price_changed = $data_got["is_price_changed"];
			}

			if (isset($data_got["old_due_date"])) {

				$roleArray_data["old_due_date"] = $data_got["old_due_date"];
				$roleArray_data["is_due_changed"] = $data_got["is_due_changed"];

				// $milestone_logs_table->old_due_date = $data_got["old_due_date"];
				// $milestone_logs_table->is_due_changed = $data_got["is_due_changed"];
			}
		}



		$milestone_logs_table = $this->ProjectMilestonesLogs->patchEntity($milestone_logs_table, $roleArray_data);


		if ($this->ProjectMilestonesLogs->save($milestone_logs_table)) {
			return true;
		} else {
			return false;
		}
	}


	public function getuserdata()
	{



		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		// $role = $userSession['role'];
		// $user_id = $parent_id = ($role==1)?$userSession['id']:$userSession['parent_id'];

		return $userSession;
	}

	public function checkDueDate($miles_obj, $request_data)
	{
		$obj = date('Y-m-d', strtotime($miles_obj->due_date));
		$custom_date = date("Y-m-d", strtotime($request_data["due_date"]));
		if ($obj != $custom_date) {

			return true;
		} else {

			return false;
		}
	}

	public function checkPrice($miles_obj, $request_data)
	{
		$obj = (float)$miles_obj->amount;
		if ($obj != (float)$request_data["amount"]) {
			return true;
		} else {
			return false;
		}
	}



	// public function addMilestone()
	// {
	// 	$this->autoRender = false;
	// 	$this->Authorization->skipAuthorization();
	// 	$conn = ConnectionManager::get('default');
	// 	$this->Projects = $this->fetchTable('Projects');
	// 	$this->ProjectMilestones = $this->fetchTable('ProjectMilestones');
	// 	$this->MilestoneExtend = $this->fetchTable('MilestoneExtend');
	// 	$mile = $this->ProjectMilestones->newEmptyEntity();

	// 	if ($this->request->is('ajax')) {
	// 		// echo '<pre>';
	// 		// print_r($this->request->getData());
	// 		// die;

	// 		$mile = $this->ProjectMilestones->patchEntity($mile, $this->request->getData());
	// 		$mile->due_date = date('Y-m-d', strtotime($this->request->getData('due_date')));
	// 		//$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
	// 		//$mile->due_date = $due_date;
	// 		if ($result = $this->ProjectMilestones->save($mile)) {

	// 			//add this value in project table
	// 			if (!empty($this->request->getData('project_id'))) {
	// 				$r = $this->Projects->find('all')
	// 					->select(['milestone_id', 'due_date'])
	// 					->where(['id' => $this->request->getData('project_id')])
	// 					->first();

	// 				$mid = ($r->milestone_id == '') ? $result->id : $r->milestone_id . ',' . $result->id;
	// 				if (strtotime($this->request->getData('due_date')) > strtotime($r->due_date)) {
	// 					$extend = $this->MilestoneExtend->newEmptyEntity();
	// 					$extend->project_id = (int) $this->request->getData('project_id');
	// 					$extend->extend_date = date('Y-m-d', strtotime($this->request->getData('due_date')));
	// 					$this->MilestoneExtend->save($extend);
	// 					$query = "UPDATE projects SET milestone_id='" . $mid . "', extend_date='" .  date('Y-m-d', strtotime($this->request->getData('due_date'))) . "' WHERE id=" . $this->request->getData('project_id');
	// 				} else {
	// 					$query = "UPDATE projects SET milestone_id='" . $mid . "' WHERE id=" . $this->request->getData('project_id');
	// 				}
	// 				$conn->execute($query);
	// 			}

	// 			$data = array(
	// 				'id' => $result->id,
	// 				'title' => $this->request->getData('title'),
	// 				'due_date' => date('d F Y', strtotime($this->request->getData('due_date'))),
	// 				'amount' => $this->request->getData('amount')
	// 			);


	// 			$userSession = $this->getuserdata();
	// 			// echo '<pre>';
	// 			// print_r($userSession);
	// 			// print_r($result['id']);
	// 			// die;

	// 			$this->milestonelog($this->request->getData('project_id'), $result["id"], $userSession["id"], "Created", []);

	// 			echo json_encode($data);
	// 			die;
	// 		}
	// 	}
	// }

	public function addMilestone()
	{
		$this->autoRender = false;

		$this->Authorization->skipAuthorization();

		$conn = ConnectionManager::get('default');

		$this->Projects = $this->fetchTable('Projects');
		$this->ProjectMilestones = $this->fetchTable('ProjectMilestones');
		$this->MilestoneExtend = $this->fetchTable('MilestoneExtend');

		$mile = $this->ProjectMilestones->newEmptyEntity();

		if ($this->request->is('ajax')) {

			try {

				$mile = $this->ProjectMilestones->patchEntity(
					$mile,
					$this->request->getData()
				);

				$mile->due_date = date(
					'Y-m-d',
					strtotime($this->request->getData('due_date'))
				);

				$result = $this->ProjectMilestones->save($mile);

				if (!$result) {

					echo "<pre>";
					print_r($mile->getErrors());
					die;
				}

				if (!empty($this->request->getData('project_id'))) {

					$r = $this->Projects->find()
						->select(['milestone_id', 'due_date'])
						->where([
							'id' => $this->request->getData('project_id')
						])
						->first();

					$mid = empty($r->milestone_id)
						? $result->id
						: $r->milestone_id . ',' . $result->id;

					if (
						strtotime($this->request->getData('due_date'))
						> strtotime($r->due_date)
					) {

						$extend = $this->MilestoneExtend->newEmptyEntity();

						$extend->project_id = (int)$this->request->getData('project_id');

						$extend->extend_date = date(
							'Y-m-d',
							strtotime($this->request->getData('due_date'))
						);

						$saveExtend = $this->MilestoneExtend->save($extend);

						if (!$saveExtend) {

							echo "<pre>";
							print_r($extend->getErrors());
							die;
						}

						$query = "
							UPDATE projects
							SET
								milestone_id='$mid',
								extend_date='" . date(
									'Y-m-d',
									strtotime($this->request->getData('due_date'))
								) . "'
							WHERE id=" . $this->request->getData('project_id');

					} else {

						$query = "
							UPDATE projects
							SET milestone_id='$mid'
							WHERE id=" . $this->request->getData('project_id');
					}

					$conn->execute($query);
				}

				echo json_encode([
					'success' => true
				]);

			} catch (\Exception $e) {

				echo "<pre>";
				echo $e->getMessage();
				die;
			}
		}
	}

	public function milesaction($type, $id)
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$this->ProjectMilestones = $this->fetchTable('ProjectMilestones');
		// $this->loadModel("Projects");
		$this->Projects = $this->fetchTable("Projects");
		if ($type == 'edit') {
			$miles = $this->ProjectMilestones
				->findById($id)
				->firstOrFail();

			echo json_encode($miles);
		} else if ($type == 'copy') { 
			$miles = $this->ProjectMilestones ->findById($id) ->firstOrFail(); 
			// Create a new milestone entity 
			$newMilestone = $this->ProjectMilestones->newEmptyEntity(); 
			// Copy required fields 
			$newMilestone->title = $miles->title; 
			$newMilestone->amount = $miles->amount; 
			$newMilestone->status = 'Yet to start'; 
			$newMilestone->deleted = 0;
			$newMilestone->project_id = $miles->project_id; 
			// Add one month to the existing due date 
			if (!empty($miles->due_date)) {
        		$newDueDate = $this->getNextMonthDueDate($miles->due_date);
				$newMilestone->due_date = $newDueDate;

				// Save month and year separately
				$newMilestone->milestone_month_year = $newDueDate->format('F Y');
			} else { 
				$newMilestone->due_date = null;
				$newMilestone->milestone_month_year = null; 
			} 
			// Save the copied milestone 
			if ($SavedMilestone = $this->ProjectMilestones->save($newMilestone)) { 
				$user_data = $this->getuserdata(); 
				// Log the copied milestone 
				$this->milestonelog( $SavedMilestone->project_id, $SavedMilestone->id, $user_data["id"], "Copied" ); 
				echo json_encode([ 'success' => true, 'message' => 'Milestone copied successfully', 'data' => $SavedMilestone ]); 
			} else { 
				echo json_encode([ 'success' => false, 'message' => 'Unable to copy milestone', 'errors' => $newMilestone->getErrors() ]); 
			} 
			return; 

		} else {

			$user_data = $this->getuserdata();
			// $project_id = $this->Projects->find("all", [
			// 	"select" => [$id],
			// 	"conditions" => [
			// 		"FIND_IN_SET({$id},milestone_id)"
			// 	]
			// ])->first();
			$project_id = $this->Projects->find()
				->where([
					"FIND_IN_SET(:id, milestone_id) >" => 0
				])
				->bind(':id', $id, 'integer')
				->first();

			// // echo "<pre>";
			// echo(json_encode($project_id["id"]));
			// die;


			$entity = $this->ProjectMilestones->get($id);
			$entity->deleted = 1;
			$this->ProjectMilestones->save($entity);
			if(empty($project_id)) {
				$projectId = $entity->project_id;
			} else {
				$projectId = $project_id;
			}
			$this->milestonelog($project_id["id"], $id, $user_data["id"], "Deleted");
			echo true;
		}
	}

	private function getNextMonthDueDate($originalDate) {
		if (empty($originalDate)) {
			return null;
		}

		$year = (int)$originalDate->format('Y');
		$month = (int)$originalDate->format('m');
		$day = (int)$originalDate->format('d');

		$daysInCurrentMonth = cal_days_in_month( CAL_GREGORIAN, $month, $year );
		$nextMonth = $month + 1;
		$nextYear = $year;

		if ($nextMonth > 12) {
			$nextMonth = 1;
			$nextYear++;
		}
		
		$daysInNextMonth = cal_days_in_month( CAL_GREGORIAN, $nextMonth, $nextYear );
		/*
		* If the current date is the last day of the month,
		* keep it as the last day of the next month.
		*
		* Example:
		* 31 Jan -> 29 Feb -> 31 Mar -> 30 Apr -> 31 May
		*/
		if ($day == $daysInCurrentMonth) {
			$newDay = $daysInNextMonth;
		} else {
			// Otherwise keep the same day where possible
			$newDay = min($day, $daysInNextMonth);
		}
		return $originalDate->setDate( $nextYear, $nextMonth, $newDay );
	}

	public function updateMilestone()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$this->ProjectMilestones = $this->fetchTable('ProjectMilestones');
		$id = $this->request->getData('mile_id');
		$mile = $this->ProjectMilestones
			->findById($id)
			->firstOrFail();

		if ($this->request->is(['post', 'put'])) {

			$custom_data = [];
			$all_request_data = $this->request->getData();
			// dd($all_request_data);
			$old_date=date('m',strtotime($mile->due_date));

			// if ($this->checkDueDate($mile, $all_request_data)) {

			// 	$custom_data["old_due_date"] = $mile->due_date;
			// 	$custom_data["is_due_changed"] = 1;
			// 	$old_date=date('m',strtotime($custom_data['old_due_date']));
			// }

			if ($this->checkPrice($mile, $all_request_data)) {
				$custom_data["old_price"] = $mile->amount;
				$custom_data["is_price_changed"] = 1;
			}

			
			// dd($old_date);


			// 	echo "<pre>";
			// print_r($mile);
			// print_r($this->request->getData());
			// die;

			$this->ProjectMilestones->patchEntity($mile, $this->request->getData());
			$date = explode('/', $this->request->getData('due_date'));
			$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
			$mile->due_date = $due_date;
			$due_date_month=date('m',strtotime($due_date));
			$current_date=date('Y-m-d');
			$current_date_month=date('m',strtotime($current_date));
			// dd('current '. $current_date);
			// dd($due_date);
			// dd($current_date_month);

			if($due_date_month==$current_date_month){
				$this->ProjectMilestones->patchEntity($mile, $this->request->getData());
				$date = explode('/', $this->request->getData('due_date'));
				$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
				$mile->due_date = $due_date;
				$this->ProjectMilestones->save($mile);
				// dd($result);
			} elseif($due_date_month>$current_date_month){
				// dd($this->request->getData('due_date'));
				if($due_date_month==$old_date){
					$this->ProjectMilestones->patchEntity($mile, $this->request->getData());
					$date = explode('/', $this->request->getData('due_date'));
					$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
					$mile->due_date = $due_date;
					$this->ProjectMilestones->save($mile);
				} else {
					$mile1 = $this->ProjectMilestones->newEmptyEntity();
					$this->ProjectMilestones->patchEntity($mile1, $this->request->getData());
					$date = explode('/', $this->request->getData('due_date'));
					$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
					$mile1->due_date = $due_date;
					$this->ProjectMilestones->save($mile1);
				}
			} else {
				// $data="Please ";
				// return "Please Enter valid due date";
				// $error='Please Enter valid due date';
			    // echo json_encode($error);
				$data['amount']=$this->request->getData('amount');
				$data['title']=$this->request->getData('title');
				$this->ProjectMilestones->patchEntity($mile,$data);
				$this->ProjectMilestones->save($mile);
			}
				$data = array(
					'id' => $id,
					'title' => $this->request->getData('title'),
					'due_date' => date('d F Y', strtotime($due_date)),
					'amount' => $this->request->getData('amount')
				);

				$userSession = $this->getuserdata();


				$this->milestonelog($this->request->getData("project_id"), $data["id"], $userSession["id"], "Updated", $custom_data);

				echo json_encode($data);
			
		}
	}


	public function addPayment()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$conn = ConnectionManager::get('default');
		$this->Projects = $this->fetchTable('Projects');
		$this->ProjectPayments = $this->fetchTable('ProjectPayments');
		$payment = $this->ProjectPayments->newEmptyEntity();

		if ($this->request->is('ajax')) {
			$payment = $this->ProjectPayments->patchEntity($payment, $this->request->getData());
			$date = explode('/', $this->request->getData('payment_date'));
			$payment_date = $date[2] . '-' . $date[0] . '-' . $date[1];
			$payment->payment_date = $payment_date;
			if ($result = $this->ProjectPayments->save($payment)) {

				//add this value in project table
				if (!empty($this->request->getData('project_id'))) {
					$r = $this->Projects->find('all')
						->select(['payment_id'])
						->where(['id' => $this->request->getData('project_id')])
						->first();
					$mid = ($r->payment_id == '') ? $result->id : $r->payment_id . ',' . $result->id;
					$query = "UPDATE projects SET payment_id='" . $mid . "' WHERE id=" . $this->request->getData('project_id');
					$stmtProduct = $conn->execute($query);
				}

				$data = array(
					'id' => $result->id,
					'description' => $this->request->getData('description'),
					'payment_date' => date('d F Y', strtotime($payment_date)),
					'receive_amt' => $this->request->getData('receive_amt')
				);

				echo json_encode($data);
			}
		}
	}

	public function paymentsaction($type, $id)
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$this->ProjectPayments = $this->fetchTable('ProjectPayments');
		if ($type == 'edit') {
			$payment = $this->ProjectPayments
				->findById($id)
				->firstOrFail();

			echo json_encode($payment);
		} else {

			$entity = $this->ProjectPayments->get($id);
			$result = $this->ProjectPayments->delete($entity);
			echo true;
		}
	}

	public function updatePayment()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$this->ProjectPayments = $this->fetchTable('ProjectPayments');
		$id = $this->request->getData('payment_id');
		$payment = $this->ProjectPayments
			->findById($id)
			->firstOrFail();

		if ($this->request->is(['post', 'put'])) {
			$this->ProjectPayments->patchEntity($payment, $this->request->getData());
			$date = explode('/', $this->request->getData('payment_date'));
			$payment_date = $date[2] . '-' . $date[0] . '-' . $date[1];
			$payment->payment_date = $payment_date;
			if ($this->ProjectPayments->save($payment)) {
				$data = array(
					'id' => $id,
					'description' => $this->request->getData('description'),
					'payment_date' => date('d F Y', strtotime($payment_date)),
					'receive_amt' => $this->request->getData('receive_amt'),
					'status' => $payment->status
				);

				echo json_encode($data);
			}
		}
	}

	public function status($id, $status, $type)
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		if ($this->request->is('ajax')) {
			$this->Projects = $this->fetchTable('Projects');
			$this->ProjectMilestones = $this->fetchTable('ProjectMilestones');
			$this->ProjectPayments = $this->fetchTable('ProjectPayments');
			$this->ProjectTasks = $this->fetchTable('ProjectTasks');

			if ($type == 'project') {
				// $query = $this->Projects->query();
				// $query->update()
				// 	->set(['status' => $status])
				// 	->where(['id' => $id]);
				$query = $this->Projects->updateQuery();

				$query
					->set(['status' => $status])
					->where(['id' => $id]);
				if ($query->execute())
					echo 1;
				else
					echo 0;
				die;
			}
			if ($type == 'miles') {
				// $query = $this->ProjectMilestones->query();

				// $query->update()
				// 	->set(['status' => $status])
				// 	->where(['id' => $id])
				// 	->execute();

				$query = $this->ProjectMilestones->updateQuery();

				$query
					->set(['status' => $status])
					->where(['id' => $id]);

				if ($query->execute()) {
					echo 1;
				} else {
					echo 0;
				}
			}
			if ($type == 'payment') {
				// $query = $this->ProjectPayments->query();

				// $query->update()
				// 	->set(['status' => $status])
				// 	->where(['id' => $id])
				// 	->execute();

				$query = $this->ProjectPayments->updateQuery();

				$query
					->set(['status' => $status])
					->where(['id' => $id]);

				if ($query->execute()) {
					echo 1;
				} else {
					echo 0;
				}
			}
			if ($type == 'tasks') {
				// $query = $this->ProjectTasks->query();

				// $query->update()
				// 	->set(['status' => $status])
				// 	->where(['id' => $id])
				// 	->execute();

				$query = $this->ProjectTasks->updateQuery();

				$query
					->set(['status' => $status])
					->where(['id' => $id]);

				if ($query->execute()) {
					echo 1;
				} else {
					echo 0;
				}
			}

			die();
		}
	}


	public function addTask()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();

		$this->ProjectTasks = $this->fetchTable('ProjectTasks');
		$tasks = $this->ProjectTasks->newEmptyEntity();

		if ($this->request->is('ajax')) {
			$tasks = $this->ProjectTasks->patchEntity($tasks, $this->request->getData());
			if (!empty($this->request->getData('due_date'))) {
				$date = explode('/', $this->request->getData('due_date'));
				$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
				$tasks->due_date = $due_date;
			} else {
				$tasks->due_date = '1800-01-01';
				$due_date = "";
			}

			if ($result = $this->ProjectTasks->save($tasks)) {

				$data = array(
					'id' => $result->id,
					'task' => $this->request->getData('task'),
					'due_date' => !empty($due_date) ? date('d F Y', strtotime($due_date)) : '',
					'milestone_id' => $this->request->getData('milestone_id')
				);

				echo json_encode($data);
			}
		}
	}


	public function tasksaction($type, $id)
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$this->ProjectTasks = $this->fetchTable('ProjectTasks');
		if ($type == 'edit') {
			$tasks = $this->ProjectTasks
				->findById($id)
				->firstOrFail();

			echo json_encode($tasks);
		} else {

			$entity = $this->ProjectTasks->get($id);
			$result = $this->ProjectTasks->delete($entity);
			echo true;
		}
	}


	public function updateTask()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$this->ProjectTasks = $this->fetchTable('ProjectTasks');
		$id = $this->request->getData('task_id');
		$tasks = $this->ProjectTasks
			->findById($id)
			->firstOrFail();

		if ($this->request->is(['post', 'put'])) {
			$this->ProjectTasks->patchEntity($tasks, $this->request->getData());
			if (!empty($this->request->getData('due_date'))) {
				$date = explode('/', $this->request->getData('due_date'));
				$due_date = $date[2] . '-' . $date[0] . '-' . $date[1];
				$tasks->due_date = $due_date;
			} else {
				$tasks->due_date = '1800-01-01';
				$due_date = "";
			}
			if ($this->ProjectTasks->save($tasks)) {
				$data = array(
					'id' => $id,
					'task' => $this->request->getData('task'),
					'due_date' => !empty($due_date) ? date('d F Y', strtotime($due_date)) : '',
					'milestone_id' => $this->request->getData('milestone_id')
				);

				echo json_encode($data);
			}
		}
	}

	public function allotment($id, $val, $resource)
	{
		// echo $id , $val, $resource;
		// die;
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();
		$conn = ConnectionManager::get('default');

		$query = "SELECT id FROM project_allocations WHERE milestone_id=" . $id . " AND resource_id=" . $resource;
		$stmtProduct = $conn->execute($query);
		$mlist = $stmtProduct->fetchAll('assoc');

		if (count($mlist) > 0) {
			$query = "UPDATE project_allocations SET time_slot=" . $val . " WHERE id=" . $mlist[0]['id'];
			$stmtProduct = $conn->execute($query);
		} else {
			$query = "INSERT INTO project_allocations(milestone_id,resource_id,time_slot) VALUES(" . $id . "," . $resource . "," . $val . ")";
			$stmtProduct = $conn->execute($query);
		}
	}

	//for timesheet record
	public function timesheetRecord()
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$this->Authorization->skipAuthorization();
		// $this->loadComponent('Paginator');
		$this->paginate();

		$session = new \Cake\Http\Session();
	}

	public function managerReport()
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$this->Authorization->skipAuthorization();
		// $this->loadComponent('Paginator');
		$this->paginate();

		$session = new \Cake\Http\Session();
	}

	public function changeActivation($val = null)
	{
		$value = $this->request->getQuery('val');

		$this->viewBuilder()->setLayout('default_new');
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$this->Projects = $this->fetchTable('Projects');

		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		$parent_id = $userSession['parent_id'];
		// $user_id = $this->request->getAttribute('identity')->getIdentifier();
		$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.status != 'Completed' AND p.active = $val AND (p.project_manager_id=" . $user_id . " OR p.tech_lead_id=" . $user_id . " OR p.bd_id=" . $user_id . " OR FIND_IN_SET(" . $user_id . ",resources)!=0)";
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');

		$projects = array();
		$total = $complete = $active = 0;
		foreach ($list as $l) {
			if ($l['active'] == $value) {
				//calculate total amount from milestone
				$amount = $overdue = $due = 0;
				if ($l['milestone_id']) {
					$query = "SELECT p.amount FROM project_milestones p WHERE p.id IN (" . $l['milestone_id'] . ") and deleted = 0";
					$stmtProduct = $conn->execute($query);
					$mlist = $stmtProduct->fetchAll('assoc');

					foreach ($mlist as $m) {
						$amount += $m['amount'];
					}

					//count overdue and due mile 
					$query = "SELECT SUM(CASE when (due_date < '" . date('Y-m-d') . "' AND status != 'Completed') then 1 else 0 END) as overdue,SUM(CASE when ((due_date >= '" . date('Y-m-d') . "' AND due_date <= '" . date('Y-m-d', strtotime('+15 days')) . "') AND status != 'Completed') then 1 else 0 END) as due FROM project_milestones p WHERE p.id IN (" . $l['milestone_id'] . ")";
					$stmtProduct = $conn->execute($query);
					$mlist = $stmtProduct->fetchAll('assoc');
					$overdue = $mlist[0]['overdue'];
					$due = $mlist[0]['due'];
				}
				//calculate paid amount from payment history
				$paid = 0;
				if ($l['payment_id']) {
					$query = "SELECT p.receive_amt FROM project_payments p WHERE p.id IN (" . $l['payment_id'] . ") AND status='Paid'";
					$stmtProduct = $conn->execute($query);
					$plist = $stmtProduct->fetchAll('assoc');

					foreach ($plist as $p) {
						$paid += $p['receive_amt'];
					}
				}

				// new function

				$pm_amount = "SELECT 
								SUM(project_milestones.amount) AS t_amount,
								projects.id AS projId,
								projects.project_name 
							FROM project_milestones 
							LEFT JOIN projects 
								ON projects.id = project_milestones.project_id 
							WHERE projects.id = :id 
								AND project_milestones.deleted = 0";

				$stmtProduct = $conn->execute($pm_amount, [
					'id' => $l['id']
				]);

				$pm_amount = $stmtProduct->fetchAll('assoc');
				// dd($pm_amount[0]['t_amount']);

				$actual_hours = "SELECT sum(user_timesheets.time_used) as time_used FROM `user_timesheets` LEFT JOIN project_milestones ON project_milestones.id=user_timesheets.milestone_id LEFT JOIN projects ON projects.id=project_milestones.project_id WHERE projects.id=".$l['id'];
				$stmtProduct = $conn->execute($actual_hours);
				$actual_hours = $stmtProduct->fetchAll('assoc');
				
				$allocated_hours = "SELECT sum(project_allocations.time_slot) as time_slot FROM `project_allocations` LEFT JOIN project_milestones ON project_allocations.milestone_id=project_milestones.id LEFT JOIN projects ON project_milestones.project_id=projects.id WHERE projects.id=".$l['id'];
				$stmtProduct = $conn->execute($allocated_hours);
				$allocated_hours = $stmtProduct->fetchAll('assoc');

				$p['id'] = $l['id'];
				$p['project_name'] = $l['project_name'];
				$p['client_id']    = $l['client_id'];
				$p['client']    = $l['client_name'];
				$p['project_manager']    = $l['project_manager'];
				$p['award']   = date('d F Y', strtotime($l['awarded_on']));
				$p['due_date'] = date('d-m-Y', strtotime($l['due_date']));
				$p['type'] = $l['project_type'];
				$p['amount'] = $amount;
				$p['paid'] = $paid;
				$p['status'] = $l['status'];
				$p['active'] = $l['active'];
				$p['overdue'] = $overdue;
				$p['due'] = $due;
				$p['pm_amount']=$pm_amount[0]['t_amount'];
				$p['actual_hours']= round($actual_hours[0]['time_used']);
				$p['allocated_hours']= round($allocated_hours[0]['time_slot']);
				if($l['hourly_rate']==0){
					$p['budget']='Na';
				} else {
					$p['budget'] = round($pm_amount[0]['t_amount']/$l['hourly_rate']);
				}

				$projects[] = $p;

				$total++;
				if ($l['status'] == 'Pending')
					$active++;
				else
					$complete++;
			}
		}

		echo json_encode($projects);

		die;
	}

	public function projectDetails($val = null)
	{
		$value = $this->request->getQuery('proDetails');

		$this->viewBuilder()->setLayout('default_new');
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$this->Projects = $this->fetchTable('Projects');

		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		$parent_id = $userSession['parent_id'];
		// $user_id = $this->request->getAttribute('identity')->getIdentifier();
		$query = "SELECT p.*,c.client_name,pm.name as project_manager FROM projects p LEFT JOIN users c ON p.client_id = c.id LEFT JOIN users pm ON p.project_manager_id=pm.id WHERE p.deleted=1 AND p.status = '{$val}' AND p.active = 1 AND (p.project_manager_id=" . $user_id . " OR p.tech_lead_id=" . $user_id . " OR p.bd_id=" . $user_id . " OR FIND_IN_SET(" . $user_id . ",resources)!=0)";
		$stmtProduct = $conn->execute($query);
		$list = $stmtProduct->fetchAll('assoc');

		$projects = array();
		$total = $complete = $active = 0;
		foreach ($list as $l) {
			if ($l['status'] == $value) {
				//calculate total amount from milestone
				$amount = $overdue = $due = 0;
				if ($l['milestone_id']) {
					$query = "SELECT p.amount FROM project_milestones p WHERE p.id IN (" . $l['milestone_id'] . ") and deleted = 0";
					$stmtProduct = $conn->execute($query);
					$mlist = $stmtProduct->fetchAll('assoc');

					foreach ($mlist as $m) {
						$amount += $m['amount'];
					}

					//count overdue and due mile 
					$query = "SELECT SUM(CASE when (due_date < '" . date('Y-m-d') . "' AND status != 'Completed') then 1 else 0 END) as overdue,SUM(CASE when ((due_date >= '" . date('Y-m-d') . "' AND due_date <= '" . date('Y-m-d', strtotime('+15 days')) . "') AND status != 'Completed') then 1 else 0 END) as due FROM project_milestones p WHERE p.id IN (" . $l['milestone_id'] . ")";
					$stmtProduct = $conn->execute($query);
					$mlist = $stmtProduct->fetchAll('assoc');
					$overdue = $mlist[0]['overdue'];
					$due = $mlist[0]['due'];
				}
				//calculate paid amount from payment history
				$paid = 0;
				if ($l['payment_id']) {
					$query = "SELECT p.receive_amt FROM project_payments p WHERE p.id IN (" . $l['payment_id'] . ") AND status='Paid'";
					$stmtProduct = $conn->execute($query);
					$plist = $stmtProduct->fetchAll('assoc');

					foreach ($plist as $p) {
						$paid += $p['receive_amt'];
					}
				}

				// new function

				$pm_amount = "SELECT 
								SUM(project_milestones.amount) AS t_amount,
								projects.id AS projId,
								projects.project_name 
							FROM project_milestones 
							LEFT JOIN projects 
								ON projects.id = project_milestones.project_id 
							WHERE projects.id = :id 
								AND project_milestones.deleted = 0";

				$stmtProduct = $conn->execute($pm_amount, [
					'id' => $l['id']
				]);

				$pm_amount = $stmtProduct->fetchAll('assoc');
				// dd($pm_amount[0]['t_amount']);

				$actual_hours = "SELECT sum(user_timesheets.time_used) as time_used FROM `user_timesheets` LEFT JOIN project_milestones ON project_milestones.id=user_timesheets.milestone_id LEFT JOIN projects ON projects.id=project_milestones.project_id WHERE projects.id=".$l['id'];
				$stmtProduct = $conn->execute($actual_hours);
				$actual_hours = $stmtProduct->fetchAll('assoc');
				
				$allocated_hours = "SELECT sum(project_allocations.time_slot) as time_slot FROM `project_allocations` LEFT JOIN project_milestones ON project_allocations.milestone_id=project_milestones.id LEFT JOIN projects ON project_milestones.project_id=projects.id WHERE projects.id=".$l['id'];
				$stmtProduct = $conn->execute($allocated_hours);
				$allocated_hours = $stmtProduct->fetchAll('assoc');

				$p['id'] = $l['id'];
				$p['project_name'] = $l['project_name'];
				$p['client_id']    = $l['client_id'];
				$p['client']    = $l['client_name'];
				$p['project_manager']    = $l['project_manager'];
				$p['award']   = date('d F Y', strtotime($l['awarded_on']));
				$p['due_date'] = date('d-m-Y', strtotime($l['due_date']));
				$p['type'] = $l['project_type'];
				$p['amount'] = $amount;
				$p['paid'] = $paid;
				$p['status'] = $l['status'];
				$p['active'] = $l['active'];
				$p['overdue'] = $overdue;
				$p['due'] = $due;
				$p['pm_amount']=$pm_amount[0]['t_amount'];
				$p['actual_hours']= round($actual_hours[0]['time_used']);
				$p['allocated_hours']= round($allocated_hours[0]['time_slot']);
				if($l['hourly_rate']==0){
					$p['budget']='Na';
				} else {
					$p['budget'] = round($pm_amount[0]['t_amount']/$l['hourly_rate']);
				}

				$projects[] = $p;

				$total++;
				if ($l['status'] == 'Pending')
					$active++;
				else
					$complete++;
			}
		}

		echo json_encode($projects);

		die;
	}

	public function allMilestone($id = null)
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$this->Projects = $this->fetchTable('Projects');
		$this->Users = $this->fetchTable('Users');

		$mId = $this->request->getSession()->read('managerId');
		// $userId = $this->request->getSession()->read('userId');
		$page = $this->request->getSession()->read('page');

		$this->request->getSession()->delete('managerId');
		$this->request->getSession()->delete('page');

		// echo $mId;
		// echo $page;
		// die;

		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$role = $userSession['role'];
		$user_id = $parent_id = ($role == 1) ? $userSession['id'] : $userSession['parent_id'];

		if ($id) {
			if ($this->request->getQuery('mile') == 'all') {
				$query = "SELECT p.* FROM project_milestones p WHERE p.project_id =" . $id . " AND deleted=0";
			} else {
				$query = "SELECT p.* FROM project_milestones p WHERE p.project_id =" . $id . " AND deleted=0 AND status != 'Completed' ";
			}

			$stmtProduct = $conn->execute($query);
			$list = $stmtProduct->fetchAll('assoc');

			foreach ($list as $l) {
				$query = "SELECT p.* FROM project_tasks p WHERE p.milestone_id =" . $l['id'];
				$stmtProduct = $conn->execute($query);
				$tlist = $stmtProduct->fetchAll('assoc');
				$p['task_list'] = array();
				if (count($tlist) > 0) {
					foreach ($tlist as $tl) {
						$t['id'] = $tl['id'];
						$t['task'] = $tl['task'];
						$t['due_date'] = ($tl['due_date'] != '1800-01-01') ? date('d F Y', strtotime($tl['due_date'])) : '';
						$t['status'] = $tl['status'];
						$p['task_list'][] = $t;
					}
				}

				$p['id'] = $l['id'];
				$p['title'] = $l['title'];
				$p['due_date'] = date('d F Y', strtotime($l['due_date']));
				$p['amount'] = $l['amount'];
				$p['status'] = $l['status'];

				$miles[] = $p;
			}
		}

		echo json_encode($miles);
		die;
	}

	public function allResources()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();

		if ($this->request->is("GET")) {
			$id = $this->request->getQuery('id');
			$value = $this->request->getQuery('value');

			$mileStone = $this->getTableLocator()->get("ProjectMilestones");
			$projectAllocation = $this->getTableLocator()->get("ProjectAllocations");

			if ($value == 'all') {
				$title = $mileStone->find()
					->select(['id', 'title'])
					->where([
						'project_id' => $id,
						'deleted' => 0
					])
					->toArray();


				foreach ($title as $val) {
					// $timeSlot[] = $val->id;
					$data = $projectAllocation->find()
						->select([
							'name' => 'User.name',
							'time_slot' => 'ProjectAllocations.time_slot',
							'resource_id' => 'ProjectAllocations.resource_id',
							'milestone_id' => 'ProjectAllocations.milestone_id',
						])
						->join([
							'User' => [
								'table' => 'users',
								'type' => 'INNER',
								'conditions' => 'User.id = ProjectAllocations.resource_id'
							]
						])
						->where([
							'ProjectAllocations.milestone_id' => $val->id,
						])
						->toArray();
					if (count($data) > 0)
						$timeSlot[] = $data;
				}

				// echo json_encode($title);
				// echo json_encode($timeSlot);
				echo '<pre>';
				print_r($timeSlot);
			} else {
				$title = $mileStone->find()
					->select(['title'])
					->where([
						'project_id' => $id,
						'deleted' => 0,
						'status !=' => 'Completed',
					])
					->toArray();
				echo json_encode($title);
			}
		}
	}

	// Upwork config
	private function config()
	{
		$upworkCredential = $this->getTableLocator()->get("UpworkCredential");
		$upworkData = $upworkCredential->find()->first();
		$access_token = $upworkData->access_token;

		$config = new \Upwork\API\Config(
			array(
				'clientId'          => UW_CLIENTID,
				'clientSecret'      => UW_CLIENTSECRET,
				'redirectUri'       => 'http://44.230.62.131/pmtool/',
				'accessToken'       =>  $access_token,
			)
		);

		$client = new \Upwork\API\Client($config);
		return $client;
	}

	public function contracts()
	{
		$this->Authorization->skipAuthorization();

		$client = $this->config();

		$offers = new \Upwork\API\Routers\Hr\Freelancers\Offers($client);

		$offSet = 0; // Skip the data

		$countData = 0;

		while ($countData >= 0) {

			$params = array("status" => "accepted", "page" => "$offSet;50");

			$offSet += 50;

			$data = $offers->getList($params);

			// echo '<pre>';
			// print_r($data);
			// die;

			if (isset($data->offers->offer)) {
				$countData += 1;
				$offerData = $data->offers->offer;

				foreach ($offerData as $value) {

					$contractData = $this->UpworkContract->find()
						->where(['rid' => $value->rid])
						->toArray();

					if (count($contractData) == 0) {

						$upWorkData = $this->UpworkContract->newEmptyEntity();
						$upWorkData->rid = $value->rid;
						$upWorkData->client_user_ref = $value->client_user_ref;
						$upWorkData->client_org_ref = $value->client_org_ref;
						$upWorkData->contractor_user_ref = $value->contractor_user_ref;
						$upWorkData->contractor_org_ref = $value->contractor_org_ref;
						$upWorkData->title = $value->title;
						$upWorkData->type = $value->type;
						$upWorkData->last_event_state = $value->last_event_state;

						if ($value->terms_data->charge_amount)
							$upWorkData->charge_amount = $value->terms_data->charge_amount;

						if ($value->terms_data->charge_rate)
							$upWorkData->charge_rate = $value->terms_data->charge_rate;

						if ($value->terms_data->charge_weekly_stipend_amount)
							$upWorkData->charge_weekly_stipend_amount = $value->terms_data->charge_weekly_stipend_amount;

						if ($value->terms_data->manual_time_allowed)
							$upWorkData->manual_time_allowed = $value->terms_data->manual_time_allowed;

						if ($value->terms_data->start_date)
							$upWorkData->start_date = $value->terms_data->start_date;

						if ($value->terms_data->weekly_limit)
							$upWorkData->weekly_limit = $value->terms_data->weekly_limit;

						$upWorkData->is_visible_to_contractor = $value->is_visible_to_contractor;
						$upWorkData->job_posting_ref = $value->job_posting_ref;

						$this->UpworkContract->save($upWorkData);
					} else {

						if ($value->terms_data->charge_amount)
							$charge_amount = $value->terms_data->charge_amount;
						else
							$charge_amount = null;

						if ($value->terms_data->charge_rate)
							$charge_rate = $value->terms_data->charge_rate;
						else
							$charge_rate = null;

						if ($value->terms_data->charge_weekly_stipend_amount)
							$charge_weekly_stipend_amount = $value->terms_data->charge_weekly_stipend_amount;
						else
							$charge_weekly_stipend_amount = null;

						if ($value->terms_data->manual_time_allowed)
							$manual_time_allowed = $value->terms_data->manual_time_allowed;
						else
							$manual_time_allowed = null;

						if ($value->terms_data->start_date)
							$start_date = $value->terms_data->start_date;
						else
							$start_date = null;

						if ($value->terms_data->weekly_limit)
							$weekly_limit = $value->terms_data->weekly_limit;
						else
							$weekly_limit = null;


						$updateData = $this->UpworkContract->query()
							->update()
							->set([
								'rid' => $value->rid,
								'client_user_ref' => $value->client_user_ref,
								'client_org_ref' => $value->client_org_ref,
								'contractor_user_ref' => $value->contractor_user_ref,
								'contractor_org_ref' => $value->contractor_org_ref,
								'title' => $value->title,
								'type' => $value->type,
								'last_event_state' => $value->last_event_state,
								'charge_amount' => $charge_amount,
								'charge_rate' => $charge_rate,
								'charge_weekly_stipend_amount' => $charge_weekly_stipend_amount,
								'start_date' => $start_date,
								'manual_time_allowed' => $manual_time_allowed,
								'weekly_limit' => $weekly_limit,
								'is_visible_to_contractor' => $value->is_visible_to_contractor,
								'job_posting_ref' => $value->job_posting_ref,
							])
							->where(['id' => $contractData[0]->id, 'rid' => $contractData[0]->rid]);
						$updateData->execute();
					}
				}
			} else {
				$countData = -1;
				// echo $countData;
			}

			// echo '<pre>';
			// print_r($offerData);
			// die;
		}

		echo "Done & exit...";
		die;
	}

	// Fetch Engagement list
	public function engagementList()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();

		$client = $this->config();

		$engagements = new \Upwork\API\Routers\Hr\Engagements($client);

		$offSet = 0;

		$countData = 0;

		while ($countData >= 0) {

			$params = array("page" => "$offSet;100");
			$offSet += 100;
			$data = $engagements->getList($params)->engagements;

			// echo '<pre>';
			// print_r($data);
			// die;

			if (isset($data->engagement)) {
				$countData += 1;
				$engData = $data->engagement;
				// echo '<pre>';
				// print_r($engData);
				// die;
				foreach ($engData as $value) {

					$engData = $this->UpworkEngagementList->find()
						->where(['reference' => $value->reference])
						->toArray();

					if (count($engData) == 0) {

						$eng = $this->UpworkEngagementList->newEmptyEntity();

						$eng->provider__id = $value->provider__id;
						$eng->provider_team__id = $value->provider_team__id;
						$eng->job_ref_ciphertext = $value->job_ref_ciphertext;
						$eng->created_time = $value->created_time;
						$eng->category_uid = $value->category_uid;
						$eng->buyer_team__id = $value->buyer_team__id;
						$eng->provider_team__reference = $value->provider_team__reference;
						$eng->status = $value->status;
						$eng->dev_recno_ciphertext = $value->dev_recno_ciphertext;
						$eng->provider__reference = $value->provider__reference;
						$eng->parent_reference = $value->parent_reference;
						$eng->reference = $value->reference;
						$eng->engagement_start_date = $value->engagement_start_date;
						$eng->engagement_title = $value->engagement_title;
						$eng->engagement_end_date = $value->engagement_end_date;
						$eng->cj_job_application_uid = $value->cj_job_application_uid;
						$eng->engagement_job_type = $value->engagement_job_type;
						$eng->offer_id = $value->offer_id;
						$eng->job_application_ref = $value->job_application_ref;
						$eng->engagement_end_ts = $value->engagement_end_ts;
						$eng->job__title = $value->job__title;
						$eng->fixed_charge_amount_agreed = $value->provider__id;
						$eng->engagement_start_ts = $value->engagement_start_ts;
						$eng->buyer_team__reference = $value->buyer_team__reference;
						$eng->fixed_price_upfront_payment = $value->fixed_price_upfront_payment;
						$eng->category_name = $value->category_name;

						$this->UpworkEngagementList->save($eng);
					} else {
						$this->UpworkEngagementList->query()
							->update()
							->set([
								'provider__id' => $value->provider__id,
								'provider_team__id' => $value->provider_team__id,
								'job_ref_ciphertext' => $value->job_ref_ciphertext,
								'created_time' => $value->created_time,
								'category_uid' => $value->category_uid,
								'buyer_team__id' => $value->buyer_team__id,
								'provider_team__reference' => $value->provider_team__reference,
								'status' => $value->status,
								'dev_recno_ciphertext' => $value->dev_recno_ciphertext,
								'provider__reference' => $value->provider__reference,
								'parent_reference' => $value->parent_reference,
								'reference' => $value->reference,
								'engagement_start_date' => $value->engagement_start_date,
								'engagement_title' => $value->engagement_title,
								'engagement_end_date' => $value->engagement_end_date,
								'cj_job_application_uid' => $value->cj_job_application_uid,
								'engagement_job_type' => $value->engagement_job_type,
								'offer_id' => $value->offer_id,
								'job_application_ref' => $value->job_application_ref,
								'engagement_end_ts' => $value->engagement_end_ts,
								'job__title' => $value->job__title,
								'fixed_charge_amount_agreed' => $value->provider__id,
								'engagement_start_ts' => $value->engagement_start_ts,
								'buyer_team__reference' => $value->buyer_team__reference,
								'fixed_price_upfront_payment' => $value->fixed_price_upfront_payment,
								'category_name' => $value->category_name,
							])
							->where(['id' => $engData[0]->id, 'reference' => $engData[0]->reference, 'status' => 'active'])
							->execute();
					}
				}
			} else {
				$countData = -1;
			}
		}

		echo 'Done & exit...';
		die;
	}

	// Fetch milestone list
	public function milestone()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();

		$client = $this->config();

		$engData = $this->UpworkEngagementList->find()->select('reference')->where(['status' => 'active'])->toArray();

		// echo '<pre>';
		// print_r(count($engData));
		// die;

		if (count($engData) > 0) {
			foreach ($engData as $refVal) {

				// $milestones = new \Upwork\API\Routers\Hr\Milestones($client);
				// $mileData = $milestones->getActiveMilestone($refVal->reference);

				$engagements = new \Upwork\API\Routers\Hr\Engagements($client);
				$data = $engagements->getSpecific($refVal->reference);
				if (!empty($data->engagement->milestones)) {
					$mileStone = $data->engagement->milestones->milestone;

					// echo '<pre>';
					// print_r($mileStone);
					// die;

					foreach ($mileStone as $mileVal) {
						if ($mileVal->id) {

							$mileData = $this->UpworkMilestone->find()
								->where(['milestone_id' => $mileVal->id])
								->toArray();

							if (count($mileData) == 0) {

								$mile = $this->UpworkMilestone->newEmptyEntity();

								$mile->reference = $refVal->reference;
								$mile->milestone_id = $mileVal->id;
								$mile->state = $mileVal->state;
								$mile->due_date = $mileVal->due_date;
								$mile->deposit_amount = $mileVal->deposit_amount;
								$mile->description = $mileVal->description;

								$this->UpworkMilestone->save($mile);
							} else {
								$this->UpworkMilestone->query()
									->update()
									->set([
										'reference' => $refVal->reference,
										'milestone_id' => $mileVal->id,
										'state' => $mileVal->state,
										'due_date' => $mileVal->due_date,
										'deposit_amount' => $mileVal->deposit_amount,
										'description' => $mileVal->description,
									])
									->where(['milestone_id' => $mileVal->id])
									->execute();
							}
						}
					}
				}
			}
		}

		echo "Done & exit...";
		die;
	}

	// Upwork Refresh Token
	public function upworkRefreshToken()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();

		$upworkCredential = $this->getTableLocator()->get("UpworkCredential");

		$upworkData = $upworkCredential->find()->first();
		// $access_token = $upworkData->access_token;
		$refresh_token = $upworkData->refresh_token;

		$curl = curl_init();

		curl_setopt_array($curl, [

			CURLOPT_URL => "https://www.upwork.com/api/v3/oauth2/token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'grant_type=refresh_token&client_id=' . UW_CLIENTID . '&client_secret=' . UW_CLIENTSECRET . '&refresh_token=' . $refresh_token,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/x-www-form-urlencoded',
				'Cookie: __cf_bm=itfFBZsJOABcPolgIyjU9r_G14lh_rvdjWDMZKolcPg-1653929289-0-ATrcrEe3bjMt3i3SLkvGAFdY2oawL5PSomh/iOcTtAUhKyyS8JgBY5Jx+Da/yPaI/mNdL14cRUv7NIGRBjBTBHE=; __cfruid=45bc7836142e55abd33ad56395ae5aa2e9e6a44a-1653929289; _pxhd=COSH25cbOWr-SNtUsW73vUN3PMWNPOSIPPuNzKK-rqyjxaT0FaYkO4GMKve4WHYenN4/hd/Z7XjOqnMQbH9RBQ==:XQJIf3ojDObv8grrMVl5yC4/F0vMLk1iWF4-dPELmr13Icc89/hP0hEQIte8VmNUTnbxjlJy8F-PXLxDDKk2EKZBSuf2OS6dRv4XJ8hoQAw='
			]
		]);


		$response = curl_exec($curl);
		curl_close($curl);

		$refresh_token = json_decode($response);

		$newAccessToken = $refresh_token->access_token;
		$newRefreshToken = $refresh_token->refresh_token;
		$tokenType = $refresh_token->token_type;
		$expiresIn = $refresh_token->expires_in;

		if (isset($newAccessToken)) {
			$update = $upworkCredential->query()
				->update()
				->set([
					'access_token' => $newAccessToken,
					'refresh_token' => $newRefreshToken,
					'token_type' => $tokenType,
					'expires_in' => $expiresIn
				])
				->where(['id' => $upworkData->id]);
			if ($update->execute())
				echo "Token Update successfully!";
		}
		die;
	}

	// Upwork data for on UI
	public function upworkData()
	{
		$this->Authorization->skipAuthorization();
		$this->viewBuilder()->setLayout('default_new');

		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');

		// validation for valid user
		$roleArray = $userSession['role_name'];
		$validList = [4, 6, 9, 10];
		$this->routeValidation($roleArray,$validList);

		$resultPerPage = 50;
		// $currentPage = isset($_GET['page']) ? $this->request->getQuery('page') : 1;
		$currentPage = $this->request->getQuery('page') ? $this->request->getQuery('page') : 1;

		// Offset or skip result
		// $skip = $resultPerPage * ($currentPage - 1);

		$uwData = $this->UpworkEngagementList->find()
			->select([
				'engagement_title' => 'UpworkEngagementList.engagement_title',
				'engagement_job_type' => 'UpworkEngagementList.engagement_job_type',
				'contract_status' => 'UpworkEngagementList.status',
				'fixed_charge_amount_agreed' => 'UpworkEngagementList.fixed_charge_amount_agreed',
				'fixed_price_upfront_payment' => 'UpworkEngagementList.fixed_price_upfront_payment',
				'reference' => 'UpworkEngagementList.reference',
				// 'description' => 'MileStone.description',
				// 'deposit_amount' => 'MileStone.deposit_amount',
				// 'state' => 'MileStone.state',
				// 'due_date' => 'MileStone.due_date',
				'charge_rate' => 'con.charge_rate',
			])
			->join([
				// 'MileStone' => [
				// 	'table' => 'upwork_milestone',
				// 	'type' => 'LEFT',
				// 	'conditions' => 'MileStone.reference = UpworkEngagementList.reference'
				// ],
				'con' => [
					'table' => 'upwork_contract',
					'type' => 'LEFT',
					'conditions' => 'con.rid = UpworkEngagementList.offer_id'
				],
			])
			->limit($resultPerPage)
			->page($currentPage)
			->toArray();

			// echo "<pre>";
			// print_r($uwData);
			// echo "</pre>";			
			
			// $filterstatedata=$this->UpworkMilestone->find()->select(['state'])
			// ->distinct(['state'])
			// ->toArray();

			// print_r($resultPerPage);
			// print_r($currentPage);
			// print_r($filterstatedata);
			// die("test");
			
			// dd($filterstatedata);
		// $uwData = $this->paginate($uwData, ['limit' => 50]);
		// echo '<pre>';
		// print_r($uwData);
		// die;
		$this->set(compact('uwData', 'resultPerPage', 'currentPage'));
	}

	public function refreshUpworkData()
	{
		$offset = 700;
		do{
			$url = 'https://www.upwork.com/api/hr/v2/engagements.json?page='.$offset.";10";
			$response = $this->upworkApiResponse($url); 

			$data = json_decode($response);

			$engagementdata = $data->engagements;
			// echo "<pre>";
			// print_r($engagementdata->engagement);
			// echo "</pre>";
			$result = $engagementdata->engagement;
			$pageresult = $engagementdata->lister;
			$totalrecord = $pageresult->total_count;
			$totalitem = $pageresult->total_items;

			for($i=0; $i<$totalitem; $i++){
			// echo $result[$i]->status."</br>";
			// echo $result[$i]->reference."</br>";
				$uwData = $this->UpworkEngagementList->find()->where(['reference' => $result[$i]->reference])->first();
				if($uwData){					
					echo $result[$i]->reference."====FOUND</br>";
					$uwData->reference = $result[$i]->reference;
					$uwData->provider__id = $result[$i]->provider__id;
					$uwData->provider_team__id = $result[$i]->provider_team__id;
					$uwData->job_ref_ciphertext = $result[$i]->job_ref_ciphertext;
					$uwData->created_time = $this->convertMillisToMySQLTimestamp($result[$i]->created_time);
					$uwData->category_uid = $result[$i]->category_uid;
					$uwData->buyer_team__id = $result[$i]->buyer_team__id;
					$uwData->provider_team__reference = $result[$i]->provider_team__reference;
					$uwData->provider__reference = $result[$i]->provider__reference;
					$uwData->dev_recno_ciphertext = $result[$i]->dev_recno_ciphertext;
					$uwData->parent_reference = $result[$i]->parent_reference;
					$uwData->engagement_start_date = $this->convertMillisToMySQLTimestamp($result[$i]->engagement_start_date);
					$uwData->engagement_title = $result[$i]->engagement_title;
					$uwData->engagement_end_date = $this->convertMillisToMySQLTimestamp($result[$i]->engagement_end_date);
					$uwData->cj_job_application_uid = $result[$i]->cj_job_application_uid;
					$uwData->engagement_job_type = $result[$i]->engagement_job_type;
					$uwData->offer_id = $result[$i]->offer_id;
					$uwData->job_application_ref = $result[$i]->job_application_ref;
					$uwData->engagement_end_ts = $this->convertMillisToMySQLTimestamp($result[$i]->engagement_end_ts);
					$uwData->job__title = $result[$i]->job__title;
					$uwData->fixed_charge_amount_agreed = $result[$i]->fixed_charge_amount_agreed;
					$uwData->engagement_start_ts = $this->convertMillisToMySQLTimestamp($result[$i]->engagement_start_ts);
					$uwData->buyer_team__reference = $result[$i]->buyer_team__reference;
					$uwData->fixed_price_upfront_payment = $result[$i]->fixed_price_upfront_payment;
					$uwData->category_name = $result[$i]->category_name;
					$uwData->status = $result[$i]->status;
					$this->UpworkEngagementList->save($uwData);
				}else{
					// echo "====NOT IN DB</br>";
					$uweng = $this->UpworkEngagementList->newEmptyEntity();
					$uweng['reference'] = $result[$i]->reference;
					$uweng['provider__id'] = $result[$i]->provider__id;
					$uweng['provider_team__id'] = $result[$i]->provider_team__id;
					$uweng['job_ref_ciphertext'] = $result[$i]->job_ref_ciphertext;
					$uweng['created_time'] = $this->convertMillisToMySQLTimestamp($result[$i]->created_time);
					$uweng['category_uid'] = $result[$i]->category_uid;
					$uweng['buyer_team__id'] = $result[$i]->buyer_team__id;
					$uweng['provider_team__reference'] = $result[$i]->provider_team__reference;
					$uweng['provider__reference'] = $result[$i]->provider__reference;
					$uweng['dev_recno_ciphertext'] = $result[$i]->dev_recno_ciphertext;
					$uweng['parent_reference'] = $result[$i]->parent_reference;
					$uweng['engagement_start_date'] = $this->convertMillisToMySQLTimestamp($result[$i]->engagement_start_date);
					$uweng['engagement_title'] = $result[$i]->engagement_title;
					$uweng['engagement_end_date'] = $this->convertMillisToMySQLTimestamp($result[$i]->engagement_end_date);
					$uweng['cj_job_application_uid'] = $result[$i]->cj_job_application_uid;
					$uweng['engagement_job_type'] = $result[$i]->engagement_job_type;
					$uweng['offer_id'] = $result[$i]->offer_id;
					$uweng['job_application_ref'] = $result[$i]->job_application_ref;
					$uweng['engagement_end_ts'] = $this->convertMillisToMySQLTimestamp($result[$i]->engagement_end_ts);
					$uweng['job__title'] = $result[$i]->job__title;
					$uweng['fixed_charge_amount_agreed'] = $result[$i]->fixed_charge_amount_agreed;
					$uweng['engagement_start_ts'] = $this->convertMillisToMySQLTimestamp($result[$i]->engagement_start_ts);
					$uweng['buyer_team__reference'] = $result[$i]->buyer_team__reference;
					$uweng['fixed_price_upfront_payment'] = $result[$i]->fixed_price_upfront_payment;
					$uweng['category_name'] = $result[$i]->category_name;
					$uweng['status'] = $result[$i]->status;
					$this->UpworkEngagementList->save($uweng);
				}
			}

			$offset = $offset + 10;

		} while($offset<760);
			
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// print_r("---------------------------");	
		die("tttttt");

		
	}

	private function convertMillisToMySQLTimestamp($timestampMillis) {
		// Convert milliseconds to seconds
		$timestampSeconds = $timestampMillis / 1000;
	
		// Format the timestamp as MySQL datetime
		$mysqlTimestamp = date('Y-m-d H:i:s', $timestampSeconds);
	
		return $mysqlTimestamp;
	}

	private function upworkApiResponse($url){

		// Fetch data from the 'AccessCredentials Table' associated with the second connection
		$secondConnection = ConnectionManager::get('second_connection');
		$anotherTable = TableRegistry::getTableLocator()->get('AccessCredentials', [
			'connection' => $secondConnection,
		]);		
		$data = $anotherTable->find()->select(['access_token'])->first();
		$accessToken = $data->access_token;
		$options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_ENCODING => '',         // handle compressed
            CURLOPT_MAXREDIRS => 10,        // stop after 10 redirects
            CURLOPT_TIMEOUT => 0,           // time-out on response
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
              'Authorization: Bearer '.$accessToken,
           ),
        ); 

        $curl = curl_init();
        curl_setopt_array($curl, $options);   
        $response = curl_exec($curl);
        curl_close($curl);       

        return $response;
    }

	public function upworkDataFilter()
	{
		$this->autoRender = false;
		$this->Authorization->skipAuthorization();

		$value = $this->request->getQuery('value');
		$type = $this->request->getQuery('type');

		$uwData = $this->UpworkEngagementList->find()
			->select([
				'engagement_title' => 'UpworkEngagementList.engagement_title',
				'engagement_job_type' => 'UpworkEngagementList.engagement_job_type',
				'contract_status' => 'UpworkEngagementList.status',
				'fixed_charge_amount_agreed' => 'UpworkEngagementList.fixed_charge_amount_agreed',
				'fixed_price_upfront_payment' => 'UpworkEngagementList.fixed_price_upfront_payment',
				'reference' => 'UpworkEngagementList.reference',
				// 'description' => 'MileStone.description',
				// 'deposit_amount' => 'MileStone.deposit_amount',
				// 'state' => 'MileStone.state',
				// 'due_date' => 'MileStone.due_date',
				'charge_rate' => 'con.charge_rate',
			])
			->join([
				// 'MileStone' => [
				// 	'table' => 'upwork_milestone',
				// 	'type' => 'LEFT',
				// 	'conditions' => 'MileStone.reference = UpworkEngagementList.reference'
				// ],
				'con' => [
					'table' => 'upwork_contract',
					'type' => 'LEFT',
					'conditions' => 'con.rid = UpworkEngagementList.offer_id'
				],
			]);


		if ($type == "job") {
			$jobData = $uwData->where(["UpworkEngagementList.engagement_job_type LIKE" => "%$value%"])->toArray();
			echo json_encode($jobData);
			die;
		} else if($type == 'status') {
			$statusData = $uwData->where(["UpworkEngagementList.status LIKE" => "%$value%"])->toArray();
			echo json_encode($statusData);
			die;
		}
		
		// else {
		// 	$stateData = $uwData->where(["MileStone.state LIKE" => "%$value%"])->toArray();
		// 	echo json_encode($stateData);
		// 	die;
		// }
	}


	// Milestone Details
	public function milestoneDetails($ref = null)
	{
		$this->Authorization->skipAuthorization();
		$this->viewBuilder()->setLayout('default_new');

		$mileDetails = $this->UpworkMilestone->find()
			->select([
				'engagement_title' => 'UpworkEngagementList.engagement_title',
				'engagement_job_type' => 'UpworkEngagementList.engagement_job_type',
				'contract_status' => 'UpworkEngagementList.status',
				'fixed_charge_amount_agreed' => 'UpworkEngagementList.fixed_charge_amount_agreed',
				'fixed_price_upfront_payment' => 'UpworkEngagementList.fixed_price_upfront_payment',
				'reference' => 'UpworkEngagementList.reference',
				'description' => 'UpworkMilestone.description',
				'deposit_amount' => 'UpworkMilestone.deposit_amount',
				'state' => 'UpworkMilestone.state',
				'due_date' => 'UpworkMilestone.due_date',
				'charge_rate' => 'con.charge_rate',
			])
			->join([
				'UpworkEngagementList' => [
					'table' => 'upwork_engagement_list',
					'type' => 'INNER',
					'conditions' => 'UpworkEngagementList.reference = UpworkMilestone.reference'
				],
				'con' => [
					'table' => 'upwork_contract',
					'type' => 'INNER',
					'conditions' => 'con.rid = UpworkEngagementList.offer_id'
				],
			])
			->where(['UpworkMilestone.reference' => $ref])
			->toArray();

		$this->set(compact('mileDetails'));
	}

	public function opportunity($id=null)
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$roleArray = $userSession['role_name'];
		// validation for valid user
		$validList = [4];
		$this->routeValidation($roleArray,$validList);
		$stage = $this->request->getQuery('stage');
		$archive = $this->request->getQuery('status');
		if(!empty($archive)) {
			if($archive=='active') {
				$archive=0;
			} else {
				$archive=1;
			}
			$where = ['Opportunity.deleted' => $archive];
			if($archive==1){
				$archive='Inactive';
			} else {
				$archive='Active';
			}
		} else {
			$where = ['Opportunity.deleted' => '0'];
			$archive='Active';
		}
		// $where = ['Opportunity.deleted' => '0'];
		if (!empty($stage)) {
			
			if($stage=='Meet '){
				$where['stage'] = 'Meet & Present';
				$stage='Meet & Present';
			} else {
				$where['stage'] = $stage;
			}
		} else {
			$stage='Select stage';
		}
		// dd($where);
		
		if (in_array(4, $roleArray)) {
			$list = $this->Opportunity->find()
			->select([
				'opportunity_name' => 'Opportunity.opportunity_name',
				'id'           	   => 'Opportunity.id',
				'client_name'      => 'Opportunity.client_name',
				'stage'            => 'Stage.name',
				'type' 			   => 'Opportunity.type',
				'assigne_name'     => 'Users.name',
				'expected_amount'  => 'Opportunity.expected_amount',
				'probability'      => 'Opportunity.probability',
				'next_step'      => 'Opportunity.next_step',
				'expected_closed_date' => 'Opportunity.expected_closed_date',
				'deleted' => 'Opportunity.deleted',
				'probability_name' => 'Probability.name',
				'probability_percentage' => 'Probability.percentage',
				'probability_color_code' => 'Probability.color_code'
			])
			->join([
				'Users' => [
					'table' => 'users',
					'type' => 'LEFT',
					'conditions' => 'Users.id = Opportunity.assigned_to'
				],
				'Probability' => [
					'table' => 'probability',
					'type' => 'LEFT',
					'conditions' => 'Probability.id = Opportunity.probability'
				],
				'Stage' => [
					'table' => 'opportunity_stage',
					'type' => 'LEFT',
					'conditions' => 'Stage.id = Opportunity.stage'
				],
			])
			->where($where)
			->order(['Opportunity.id' => 'desc'])
			->toArray();
			// dd($list);
			// dd($this->request->getQuery('status'));
			if($archive=='Active') {
				$condition = ['Opportunity.deleted' => '0'];
			} else {
				$condition = ['Opportunity.deleted' => '1'];
			}

			$oppstage = $this->Stage->find()->where(['deleted'=>0])->toArray();
			// dd($oppstage);

			$totalActive = $this->Opportunity->find()
			->where(['Opportunity.deleted' => '0'])
			->count();
			$totalProposing = $this->Opportunity->find()
			->where([$condition,'stage' => 'Proposing'])
			->count();
			$totalProposed = $this->Opportunity->find()
			->where([$condition,'stage' => 'Proposed'])
			->count();
			$totalRg = $this->Opportunity->find()
			->where([$condition,'stage' => 'Req. Gath.'])
			->count();
			// dd($totalProposing);
			$count = count($list);
			$this->set(compact('count','list','stage','archive','totalProposing','totalProposed','totalRg','totalActive','oppstage'));
		} else {
			return $this->redirect($this->referer());
		}
	}

	public function addOpportunity($id=null, $manager_id = null)
	{

		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		$role = $userSession['role'];
		$roleArray = $userSession['role_name'];

		if (in_array(4, $roleArray)) {
			$this->Users = $this->fetchTable('Users');
			$mId = $this->request->getSession()->read('managerId');
			$parent_id = ($role == 1) ? $userSession['id'] : $userSession['parent_id'];
			$assignedList = $this->Users->find()
			->select(['name','id'])
			->where(function (QueryExpression $exp) use ($parent_id) {
				$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
					->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
				return $exp
					->add($orConditions)
					->eq('deleted', 1)
					->eq('role', 3)
					->eq("status", 1)
					->eq('company_id', $parent_id);
			})->order(["name" => "ASC"])
			->toArray();

			$probability_list = $this->Probability->find()
			->where(['Probability.deleted' => '0'])
			->toArray();

			$oppstage = $this->Stage->find()->where(['deleted'=>0])->toArray();

			if ($this->request->is(['post', 'put'])) {
				$this->Opportunity = $this->fetchTable('Opportunity');
				$opportunity = $this->Opportunity->newEmptyEntity();
				$opportunity['user_id'] = $user_id;
				$opportunity['opportunity_name'] = $this->request->getData('opportunity_name');
				$opportunity['client_name'] = $this->request->getData('client_name');
				$opportunity['assigned_to'] = $this->request->getData('assigned_to');
				$opportunity['expected_closed_date'] = date('Y-m-d', strtotime($this->request->getData('expected_closed_date')));
				$opportunity['expected_amount'] = str_replace(',', '', $this->request->getData('expected_amount'));
				$opportunity['lead_source'] = $this->request->getData('lead_source');
				$opportunity['type']  = $this->request->getData('type');
				$opportunity['stage'] = $this->request->getData('stage');
				$opportunity['probability'] = $this->request->getData('probability');
				$opportunity['forecast_category'] = $this->request->getData('forecast_category');
				$opportunity['next_step']  = $this->request->getData('next_step');
				$opportunity['description'] = $this->request->getData('description');
				if ($this->Opportunity->save($opportunity)) {
					$this->Flash->success(__('Your opportunity has been saved.'));
					return $this->redirect(['action' => 'opportunity']);
				} else {
					$this->Flash->error(__('Unable to save your opportunity. Please, try again.'));
					debug($opportunity->getErrors());
				}
			}
			$this->set(compact('assignedList','probability_list','oppstage'));
		}
		else
		{
			return $this->redirect($this->referer());
		}
	}

	// public function deleteOpportunity($id,$status)
	// {
	// 	$this->Authorization->skipAuthorization();
	// 	// if($type=='delete') {
	// 	// 	$val=1;
	// 	// } else {
	// 	// 	$val=0;
	// 	// }
	// 	$query =  $this->Opportunity->query();
	// 	$query->update()
	// 		->set(['deleted' => $status])
	// 		->where(['id' => $id]);
	// 	if ($query->execute())
	// 		echo 1;
	// 	else
	// 		echo 0;
	// 	die;
	// }


	public function deleteOpportunity($id, $status)
	{
		$this->Authorization->skipAuthorization();

		$result = $this->Opportunity->updateAll(
			['deleted' => $status],
			['id' => $id]
		);

		if ($result) {
			echo 1;
		} else {
			echo 0;
		}

		die;
	}

	public function editOpportunity($id)
	{
			// Set Layout
			$this->viewBuilder()->setLayout('default_new');
			$conn = ConnectionManager::get('default');
			$this->Authorization->skipAuthorization();
			$this->Users = $this->fetchTable('Users');
			$mId = $this->request->getSession()->read('managerId');
			$session = new \Cake\Http\Session();
			$userSession = $session->read('data');
			$user_id = $userSession['id'];
			$role = $userSession['role'];
			$roleArray = $userSession['role_name'];
			if (in_array(4, $roleArray)) {

				$parent_id = ($role == 1) ? $userSession['id'] : $userSession['parent_id'];
				$assignedList = $this->Users->find()
				->select(['name','id'])
				->where(function (QueryExpression $exp) use ($parent_id) {
					$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
						->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
					return $exp
						->add($orConditions)
						->eq('deleted', 1)
						->eq('role', 3)
						->eq("status", 1)
						->eq('company_id', $parent_id);
				})->order(["name" => "ASC"])
				->toArray();

				$probability_list = $this->Probability->find()
				->where(['Probability.deleted' => '0'])
				->toArray();

				$oppstage = $this->Stage->find()->where(['deleted'=>0])->toArray();

				// Opportunity Activity Data list
				$activityList = $this->ActivityTbl->find()->where(['opportunity_id' => $id])->toArray();
				// End 
				
				$list = $this->Opportunity->find()->where(['id' => $id])->first();

			// Edit data Post
			if ($this->request->is(['post', 'put'])) {
				$entity = $this->Opportunity->get($id);
				$entity = $this->Opportunity->patchEntity($entity, $this->request->getData());
			
				// Additional modifications or validations if needed
				$entity->user_id = $user_id;
				$entity->expected_closed_date = date('Y-m-d', strtotime($this->request->getData('expected_closed_date')));
				$entity->expected_amount = str_replace(',', '', $this->request->getData('expected_amount'));
			
				if ($this->Opportunity->save($entity)) {
					$this->Flash->success('Update Opportunity Details Successfully.');
					return $this->redirect(['action' => 'opportunity']);
				} else {
					$errors = $entity->getErrors();
					$this->Flash->error('Unable to update the record. Please check the form.');
				}
			}
			
			
				//End
				$this->set(compact('list', 'assignedList', 'id', 'activityList','probability_list','oppstage'));
			}
			else
			{
				return $this->redirect($this->referer());
			}
	}

	public function changeOpportunityStage()
	{
			// $this->viewBuilder()->setLayout('default_new');
			$this->autoRender = false;
			$conn = ConnectionManager::get('default');
			$this->Authorization->skipAuthorization();
			$id=$this->request->getData('id');
			// dd($id);
			if ($this->request->is(['post', 'put'])) {
				$entity = $this->Opportunity->get($id);
				$entity = $this->Opportunity->patchEntity($entity, $this->request->getData());

				if ($this->Opportunity->save($entity)) { 
					echo 'true';
				} else {
					echo 'false';
				}
			}
	}

	public function stage($id=null)
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$roleArray = $userSession['role_name'];
		
		
		// dd($where);
		
		if (in_array(4, $roleArray)) {
			$list = $this->Stage->find()
			->select([
				'name' => 'name',
				'id' => 'id'
			])
			->where(['deleted'=>0])
			// ->order(['Opportunity.id' => 'desc'])
			->toArray();
			$count = count($list);
			$this->set(compact('list'));
		} else {
			return $this->redirect($this->referer());
		}
	}

	public function addstage()
	{
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		
		if ($this->request->is(['patch', 'post', 'put'])) {
			$stage = $this->Stage->newEmptyEntity();
			// dd($stage);
			$stage['name'] = $this->request->getData('name');
			if ($this->Stage->save($stage)) {
				$this->Flash->success(__('Your data has been saved.'));
				return $this->redirect(['action' => 'stage']);
			}
			else
			{
				$this->Flash->error(__('Something went wrong'));
				return $this->redirect(['action' => 'stage']);
			}
		}
	}

	public function editStage()
	{
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];

        if ($this->request->is(['patch', 'post', 'put'])) {
			$stage = $this->Stage->get($this->request->getData('id'));
			$stage['name'] = $this->request->getData('name');
			if ($this->Stage->save($stage)) {
				$this->Flash->success(__('Your data has been updated.'));
				return $this->redirect(['action' => 'stage']);
			}
            $this->Flash->error(__('Plan could not be saved. Please, try again.'));
        }
	}

	public function deleteStage($id)
	{
	  	$this->Authorization->skipAuthorization();
	  		// Delete Plan Id
			$query = $this->Stage->query();
			$query->update()
				->set(['deleted' => 1])
				->where(['id' => $id]);
			if ($query->execute())
			{
				$this->Flash->success(__('Deleted Successfully!'));
				return $this->redirect(['action' => 'stage']);
			}
	}

	// start probability crud 

	public function probability($id=null)
	{
		
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$roleArray = $userSession['role_name'];
		// dd($userSession['id']);
		
		if (in_array(4, $roleArray)) {
			$list = $this->Probability->find()
			->where(['Probability.deleted' => '0'])
			// ->order(['Probability.id' => 'desc'])
			->toArray();

			if ($this->request->is(['post', 'put'])) {
				$id=$this->request->getData('id');
				$this->Probability = $this->fetchTable('Probability');
				if($id && !empty($id)) {
					$probability = $this->Probability->get($id);
				} else {
					$probability = $this->Probability->newEmptyEntity();
				}
				$probability['name'] = $this->request->getData('name');
				$probability['percentage'] = $this->request->getData('percentage');
				$probability['color_code'] = $this->request->getData('color_code');
				$probability['created_by'] = $userSession['id'];
				if ($this->Probability->save($probability)) {
					$this->Flash->success(__('Your Probability has been saved.'));
					return $this->redirect(['action' => 'probability']);
				} else {
					$this->Flash->error(__('Unable to save your Probability. Please, try again.'));
					debug($probability->getErrors());
				}
			}
			
			$count = count($list);
			$this->set(compact('count','list'));
		} else {
			return $this->redirect($this->referer());
		}
	}

	public function editprobability($id = null)
	{
			$this->Authorization->skipAuthorization();
			$this->autoRender = false; // Disable view rendering
			$this->response = $this->response->withType('application/json'); // Set response type to JSON

			if ($id !== null) {
				$data = $this->Probability->findById($id)->first();

				if (!empty($data)) {
					// echo json_encode($data);
					// // return;
					$result = json_encode($data);
					echo $result;
					exit;
				}
			}
	}

	public function changeprobabilitycolor()
	{
		$this->Authorization->skipAuthorization();
		$this->autoRender = false;
		$id=$this->request->getData('id');
		$color_code=$this->request->getData('colorpick');
		$query =  $this->Probability->query();
		$query->update()
			->set(['color_code' => $color_code])
			->where(['id' => $id]);
		if ($query->execute())
			echo 1;
		else
			echo 0;
		die;
	}

	// public function deleteprobability()
	// {
	// 	$this->Authorization->skipAuthorization();
	// 	$this->autoRender = false;
	// 	$id=$this->request->getData('id');
	// 	$query =  $this->Probability->query();
	// 	// $query->update()
	// 	// 	->set(['deleted' => 1])
	// 	// 	->where(['id' => $id]);
	// 	$query->delete()
	// 		->where(['id' => $id]);
	// 	if ($query->execute())
	// 		echo 1;
	// 	else
	// 		echo 0;
	// 	die;
	// }

	public function deleteprobability()
	{
		$this->Authorization->skipAuthorization();
		$this->autoRender = false;

		$id = $this->request->getData('id');

		$result = $this->Probability->deleteAll([
			'id' => $id
		]);

		if ($result) {
			echo 1;
		} else {
			echo 0;
		}

		die;
	}

	// Opportunity Activity Start
	public function addActivity()
	{
		$this->autoRender = false;
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];

		// Post Data 
		if ($this->request->is('post')) {
			$activity = $this->ActivityTbl->newEmptyEntity();
			$activity->user_id 		    = $user_id;
			$activity->opportunity_id   = $this->request->getData('opportunity_id');
			$activity->type_of_activity = $this->request->getData('type_of_activity');
			$activity->date_of_activity = $this->request->getData('date_of_activity');
			$activity->email_to 		= $this->request->getData('email_to');
			$activity->contacted_by     = $this->request->getData('contacted_by');
			$activity->notes            = $this->request->getData('notes');
			$referenceImage = $_FILES['reference']['name'];

			if($referenceImage):
				$uploadDir = WWW_ROOT . 'uploads' . DS;
				$originalFilename = $_FILES['reference']['name'];
				$uniqueFilename = time() . '_' . $originalFilename;
				$activityRef = str_replace(' ', '', $uniqueFilename);
				$uploadPath = $uploadDir . $activityRef;
				move_uploaded_file($_FILES['reference']['tmp_name'], $uploadPath);
				$activity->reference = $activityRef;
			endif;

			if ($this->ActivityTbl->save($activity)):
				echo 1;
			endif;
		}
		die;
	}
	// End 

	// Delete Activity 
	public function deleteActivity($id)
	{
		$this->Authorization->skipAuthorization();
		$query =  $this->ActivityTbl->query();
		$query->update()
			->set(['deleted' => 1])
			->where(['id' => $id]);
		if ($query->execute())
			echo 1;
		else
			echo 0;
		die;
	}
	// End

	public function editActivity($id = null)
	{
		if ($this->request->is('get'))
		{
			$editActivity = $this->ActivityTbl->find()->where(['id' => $id])->first();
			if (!empty($editActivity))
			{
			   $result = json_encode($editActivity);
			   echo $result;
			   exit;
			}
			else
			{
				$redirect = $this->request->getQuery('redirect', [
					'controller' => 'Companies',
					'action' => 'editOpportunity',
					$id
				]);
				return $this->redirect($redirect);
			}
		}
	}
	
	public function editActivityOpp()
	{
	
		if ($this->request->is(['post', 'put'])) {
			
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		$this->Authorization->skipAuthorization();
		$id = $this->request->getData('editId');
		$entity = $this->ActivityTbl->get($id);
		$entity->user_id 		    = $user_id;
		$oid = $this->request->getData('opportunity_id');
		$entity->opportunity_id     = $this->request->getData('opportunity_id');
		$entity->type_of_activity   = $this->request->getData('type_of_activity');
		$entity->date_of_activity   = $this->request->getData('date_of_activity');
		$entity->email_to 		    = $this->request->getData('email_to');
		$entity->contacted_by       = $this->request->getData('contacted_by');
		$entity->notes              = $this->request->getData('notes');
		$referenceImage = $_FILES['reference']['name'];

		if($referenceImage):
			$uploadDir = WWW_ROOT . 'uploads' . DS;
			$originalFilename = $_FILES['reference']['name'];
			$uniqueFilename = time() . '_' . $originalFilename;
			$activityRef = str_replace(' ', '', $uniqueFilename);
			$uploadPath = $uploadDir . $activityRef;
			move_uploaded_file($_FILES['reference']['tmp_name'], $uploadPath);
			$entity->reference = $activityRef;
		endif;
		$this->ActivityTbl->save($entity);
		$this->Flash->success('Update Successfully.');
		$redirect = $this->request->getQuery('redirect', [
			'controller' => 'Companies',
			'action' => 'editOpportunity',
			$oid
		]);
		return $this->redirect($redirect);
		exit;
	 }
	}

	// Get Complete Activity Full Notes
	public function activityNotes($id)
	{
		$this->Authorization->skipAuthorization();
		$notes = $this->ActivityTbl->find()->select(['notes'])->where(['id' => $id])->first();
		if (!empty($notes)):
			$result = $notes['notes'];
			echo $result;
			exit;
		endif;
	}

	// Support Plans functions start

	public function supportPlans($id=null)
	{
		//set layout
		$this->viewBuilder()->setLayout('default_new');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$roleArray = $userSession['role_name'];
		// validation for valid user
		$validList = [4, 6, 9, 10, 12];
		$this->routeValidation($roleArray,$validList);
		// $stage = $this->request->getQuery('stage');
		// $archive = $this->request->getQuery('status');
		// if(!empty($archive)) {
		// 	if($archive=='active') {
		// 		$archive=0;
		// 	} else {
		// 		$archive=1;
		// 	}
		// 	$where = ['Opportunity.deleted' => $archive];
		// 	if($archive==1){
		// 		$archive='Inactive';
		// 	} else {
		// 		$archive='Active';
		// 	}
		// } else {
		// 	$where = ['Opportunity.deleted' => '0'];
		// 	$archive='Active';
		// }
		// $where = ['Opportunity.deleted' => '0'];
		// if (!empty($stage)) {
			
		// 	if($stage=='Meet '){
		// 		$where['stage'] = 'Meet & Present';
		// 		$stage='Meet & Present';
		// 	} else {
		// 		$where['stage'] = $stage;
		// 	}
		// } else {
		// 	$stage='Select stage';
		// }
		// dd($where);
		$where = ['SupportPlan.deleted' => '0'];
		
		if (in_array(4, $roleArray)) {

			$do_expire = $this->SupportPlan->find()->select(['id','end_date','status'])->where(['deleted' => 0])->toArray();
			$today=date('Y-m-d');
			if(!empty($do_expire)) {
				foreach($do_expire as $value) {
					$id = $value['id'];
					$end_date = date('Y-m-d', strtotime($value['end_date']));
					if($end_date < $today) {
						// $query =  $this->SupportPlan->query();
						// 			$query->update()
						// 				->set(['status' => 0])
						// 				->where(['id' => $id]);
						// 			$query->execute();
						$this->SupportPlan->updateQuery()
						->set(['status' => 0])
						->where(['id' => $id])
						->execute();
					}
				}
			}

			$list = $this->SupportPlan->find()
			->select([
				'id' => 'SupportPlan.id',
				'name'           	   => 'Plans.plan_name',
				'client_name' => 'Clients.client_name',
				'assigne_name' => 'Assignees.name',
				'project_name'     => 'Projects.project_name',
				'start_date'  => 'SupportPlan.start_date',
				'number_of_months'      => 'SupportPlan.number_of_months',
				'end_date' => 'SupportPlan.end_date',
				'billing_peroid' => 'SupportPlan.billing_frequency',
				'amount' => 'SupportPlan.amount',
				'document' => 'SupportPlan.document',
				'created_at' => 'SupportPlan.created_at',
				'deleted' => 'SupportPlan.deleted',
				'status' => 'SupportPlan.status'
			])
			->join([
				'Assignees' => [
					'table' => 'users',
					'type' => 'LEFT',
					'conditions' => 'Assignees.id = SupportPlan.project_manager_id'
				],
				'Clients' => [
					'table' => 'users',
					'type' => 'LEFT',
					'conditions' => 'Clients.id = SupportPlan.client_id'
				],
				'Projects' => [
					'table' => 'projects',
					'type' => 'LEFT',
					'conditions' => 'Projects.id = SupportPlan.project_id'
				],
				'Plans' => [
					'table' => 'plans',
					'type' => 'LEFT',
					'conditions' => 'Plans.id = SupportPlan.plan_id'
				],
			])
			->where($where)
			->order(['SupportPlan.id' => 'desc'])
			->toArray();
			// dd($list);
			// dd($this->request->getQuery('status'));
			// if($archive=='Active') {
			// 	$condition = ['Opportunity.deleted' => '0'];
			// } else {
			// 	$condition = ['Opportunity.deleted' => '1'];
			// }

			// $totalActive = $this->Opportunity->find()
			// ->where(['Opportunity.deleted' => '0'])
			// ->count();
			// $totalProposing = $this->Opportunity->find()
			// ->where([$condition,'stage' => 'Proposing'])
			// ->count();
			// $totalProposed = $this->Opportunity->find()
			// ->where([$condition,'stage' => 'Proposed'])
			// ->count();
			// $totalRg = $this->Opportunity->find()
			// ->where([$condition,'stage' => 'Req. Gath.'])
			// ->count();
			// dd($totalProposing);
			$count = count($list);
			$this->set(compact('count','list'));
		} else {
			return $this->redirect($this->referer());
		}
	}

	public function addSupportPlans($id=null)
	{
		$this->viewBuilder()->setLayout('default_new');
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$userSession = $session->read('data');
		$user_id = $userSession['id'];
		$role = $userSession['role'];
		$roleArray = $userSession['role_name'];

		if (in_array(4, $roleArray)) {
			$this->Users = $this->fetchTable('Users');
			$this->Projects = $this->fetchTable('Projects');
			$mId = $this->request->getSession()->read('managerId');
			$parent_id = ($role == 1) ? $userSession['id'] : $userSession['parent_id'];
			$assignedList = $this->Users->find()
			->select(['name','id'])
			->where(function (QueryExpression $exp) use ($parent_id) {
				$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
					->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
				return $exp
					->add($orConditions)
					->eq('deleted', 1)
					->eq('role', 3)
					->eq("status", 1)
					->eq('company_id', $parent_id);
			})->order(["name" => "ASC"])
			->toArray();

			$projectdata = $this->Projects->find()
				->select(['Projects.project_name','Projects.id'])
				->where(['Projects.active' => 1,'Projects.deleted' => 1])
				->order(["Projects.project_name" => "ASC"])
				->toArray();

			$client_list = $this->Users->find()
			->select(['Users.client_name','Users.id'])
			->where(['status' => 1,'deleted' => 1,'role'=>2])
			->order(["name" => "ASC"])
			->toArray();

			// Plan list 
			$plan_list = $this->Plans->find()
			->select(['id','plan_name','price'])
			->where(['deleted' => 0])
			->order(["plan_name" => "ASC"])
			->toArray();
			// End 
			// dd($client_list);

			if ($this->request->is(['post', 'put'])) {

				$password = 'password';
				// $password = Security::hash($pass);

				$client = $this->Users->find()
				->select(['Users.client_name','Users.id'])
				->where(['id'=>$this->request->getData('client_id')])
				->first();
				$clientName = $client->client_name;
				$email = $this->request->getData('client_email');

				$this->SupportPlan = $this->fetchTable('SupportPlan');
				$plan = $this->SupportPlan->newEmptyEntity();
				$plan['user_id'] = $user_id;
				$plan['plan_id'] = $this->request->getData('plan_id');
				$plan['project_id'] = $this->request->getData('project_id');
				$plan['client_id'] = $this->request->getData('client_id');
				$plan['client_email'] = $this->request->getData('client_email');
				$plan['project_manager_id'] = $this->request->getData('project_manager_id');
				$plan['start_date'] = date('Y-m-d', strtotime($this->request->getData('start_date')));
				$plan['number_of_months'] = $this->request->getData('number_of_months');
				$plan['end_date'] = date('Y-m-d', strtotime($this->request->getData('end_date')));
				$plan['billing_frequency']  = $this->request->getData('billing_frequency');
				$plan['amount'] = $this->request->getData('amount');
				$plan['document'] = $this->request->getData('document');
				$plan['notes'] = $this->request->getData('notes');
				$plan_save = $this->SupportPlan->save($plan);
				if ($plan_save) {
					if($this->request->getData('billing_frequency')=='Quarterly') {
						$number_of_months=($this->request->getData('number_of_months')/3);
					} elseif($this->request->getData('billing_frequency')=='Yearly') {
						$number_of_months=($this->request->getData('number_of_months')/12);
					} else {
						$number_of_months=$this->request->getData('number_of_months');
					}
					$start_date = strtotime($this->request->getData('start_date'));
					// payment invoices Generate
					for ($i = 0; $i < $number_of_months; $i++) {
						$payment_invoices = $this->SupportPlansPayment->newEmptyEntity();
						$payment_invoices['plan_id'] = $plan_save->id;
						$payment_invoices['project_id'] = $this->request->getData('project_id');
						// Calculate start and end dates for the current invoice
						if($this->request->getData('billing_frequency')=='Quarterly') {
							if($i==0) {
								$invoice_start_date = date('Y-m-d', strtotime("+$i months", $start_date));
							} else {
								$invoice_start_date = date('Y-m-d', strtotime("+1 months", $start_date));
							}
						} elseif($this->request->getData('billing_frequency')=='Yearly') {
							$invoice_start_date = date('Y-m-d', strtotime("+$i year", $start_date));
						}
						elseif($this->request->getData('billing_frequency')=='Monthly') {
							$invoice_start_date = date('Y-m-d', strtotime("+$i months", $start_date));
						}
						if($this->request->getData('billing_frequency')=='Quarterly') {
							$invoice_end_date = date('Y-m-d', strtotime("+4 months", strtotime($invoice_start_date)));
						} elseif($this->request->getData('billing_frequency')=='Yearly') {
							$invoice_end_date = date('Y-m-d', strtotime("+1 year", strtotime($invoice_start_date)));
						} elseif($this->request->getData('billing_frequency')=='Monthly') {
							$invoice_end_date = date('Y-m-d', strtotime("+1 months", strtotime($invoice_start_date)));
						}

						$payment_invoices['start_date'] = $invoice_start_date;
    					// $payment_invoices['end_date'] = $invoice_end_date;
						$invoice_end_date = date('Y-m-d', strtotime("-1 day", strtotime($invoice_end_date)));
						$payment_invoices['end_date'] = $invoice_end_date;
						$payment_invoices['amount'] = $this->request->getData('amount');
						$invoices_save = $this->SupportPlansPayment->save($payment_invoices);
						// Update start date for the next invoice
						if($this->request->getData('billing_frequency')=='Quarterly') {
							$start_date = strtotime("+3 months", strtotime($invoice_start_date));
						} 
						// elseif($this->request->getData('billing_frequency')=='Yearly') {
						// 	$start_date = strtotime("+1 year", strtotime($invoice_start_date));
						// }
						// elseif($this->request->getData('billing_frequency')=='Monthly') {
						// 	$start_date = strtotime("+1 months", strtotime($invoice_start_date));
						// }
					}

					// Update Password in client Table 
					$passwordUpdate = $this->Users->get($client->id);
					$passwordUpdate->password = $password;
					$this->Users->save($passwordUpdate);
					// End 

					$this->Flash->success(__('Your Support Plan has been saved.'));
					$this->clientNotification($email, $clientName, $password);
					return $this->redirect(['action' => 'editSupportPlans', $plan_save->id]);
				} else {
					$this->Flash->error(__('Unable to save your Support Plan. Please, try again.'));
					debug($plan->getErrors());
				}
			}
			$this->set(compact('assignedList','projectdata','client_list','plan_list'));
		}
		else
		{
			return $this->redirect($this->referer());
		}
	}

	public function getProjectsByClient() {
		$this->autoRender = false;
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$this->Projects = $this->fetchTable('Projects');
		$this->Users = $this->fetchTable('Users');
	
		if ($this->request->is('ajax')) {
			$client_id = $this->request->getData('client_id');
			$projects['project'] = $this->Projects->find()
				->select(['id','project_name'])
				->where(['client_id'=>$client_id])
				->order(['project_name' => 'ASC'])
				->toArray();

			$projects['email'] = $this->Users->find()->select(['id','client_name','email'])->where(['id' => $client_id])->first();
	
			echo json_encode($projects);
		}
	}

	public function getProjectManager() {
		$this->autoRender = false;
		$conn = ConnectionManager::get('default');
		$this->Authorization->skipAuthorization();
		$session = new \Cake\Http\Session();
		$this->Users = $this->fetchTable('Users');
		$this->Projects = $this->fetchTable('Projects');
	
		if ($this->request->is('ajax')) {
			$project_id = $this->request->getData('project_id');
			$project_manager_id = $this->Projects->find()->where(['id' => $project_id])->extract('project_manager_id')->first();

			$project_manager = $this->Users->find()->select(['id','name'])->where(['id' => $project_manager_id])->first();
	
			// echo json_encode(['project_manager_id' => $project_manager]);
			echo json_encode($project_manager);
		}
	}

	public function editSupportPlans($id)
	{
		// dd($id);
			// Set Layout
			$this->viewBuilder()->setLayout('default_new');
			$conn = ConnectionManager::get('default');
			$this->Authorization->skipAuthorization();
			$this->Users = $this->fetchTable('Users');
			$this->Projects = $this->fetchTable('Projects');
			$mId = $this->request->getSession()->read('managerId');
			$session = new \Cake\Http\Session();
			$userSession = $session->read('data');
			$user_id = $userSession['id'];
			$role = $userSession['role'];
			$roleArray = $userSession['role_name'];
			if (in_array(4, $roleArray)) {

				$parent_id = ($role == 1) ? $userSession['id'] : $userSession['parent_id'];
				$assignedList = $this->Users->find()
				->select(['name','id'])
				->where(function (QueryExpression $exp) use ($parent_id) {
					$orConditions = $exp->or(['FIND_IN_SET(\'7\',Users.role_name) !=' => 0])
						->notEq('FIND_IN_SET(\'8\',Users.role_name)', 0);
					return $exp
						->add($orConditions)
						// ->eq('deleted', 1)
						->eq('role', 3)
						// ->eq("status", 1)
						->eq('company_id', $parent_id);
				})->order(["name" => "ASC"])
				->toArray();

				// Opportunity Activity Data list
				$payments = $this->SupportPlansPayment->find()->where(['deleted' => '0', 'plan_id' => $id])->toArray();
				// dd($payments);
				// End 
				
				$list = $this->SupportPlan->find()->where(['deleted' => '0', 'id' => $id])->first();

				$projectdata = $this->Projects->find()
				->select(['Projects.project_name','Projects.id'])
				// ->where(['Projects.active' => 1,'Projects.deleted' => 1])
				->order(["Projects.project_name" => "ASC"])
				->toArray();

				$client_list = $this->Users->find()
				->select(['Users.client_name','Users.id'])
				->where(['status' => 1,'deleted' => 1,'role'=>2])
				->order(["name" => "ASC"])
				->toArray();

				$plan_list = $this->Plans->find()
				->select(['id','plan_name','price'])
				->where(['deleted' => 0])
				->order(["plan_name" => "ASC"])
				->toArray();
				// dd($projectdata);

			// Edit data Post
			if ($this->request->is(['post', 'put'])) {
				$entity = $this->SupportPlan->get($id);
				$doc_name = $entity->document;
				$entity = $this->SupportPlan->patchEntity($entity, $this->request->getData());

				$invoices =  $this->SupportPlansPayment->find()->where(['plan_id'=> $id])->toArray();
				// dd($invoices);
				foreach ($invoices as $inc) {
					if ($inc['invoice_sent'] == 0 && $inc['invoice_paid'] == 0) {
						$entity1 = $this->SupportPlansPayment->get($inc['id']);
						$this->SupportPlansPayment->delete($entity1);
					}
				}
			
				// Additional modifications or validations if needed
				$entity->number_of_months = $this->request->getData('number_of_months');
				$entity->user_id = $user_id;
				$entity->start_date = date('Y-m-d', strtotime($this->request->getData('start_date')));
				$entity->end_date = date('Y-m-d', strtotime($this->request->getData('end_date')));
				if(!empty($this->request->getData('document'))) {
					$entity->document = $this->request->getData('document');
				} else {
					$entity->document = $doc_name;
				}
				// dd($entity);
				if ($this->SupportPlan->save($entity)) {
					// new plan

					if($this->request->getData('billing_frequency')=='Quarterly') {
						$number_of_months=($this->request->getData('number_of_months')/3);
					} elseif($this->request->getData('billing_frequency')=='Yearly') {
						$number_of_months=($this->request->getData('number_of_months')/12);
					} else {
						$number_of_months=$this->request->getData('number_of_months');
					}

					$invoices_dt =  $this->SupportPlansPayment->find()->where(['plan_id'=> $id])->order(['id'=>'desc'])->limit(1)->first();
					// dd($invoices_dt);
					if(!empty($invoices_dt)) {
						$start_date = strtotime("+1 day", strtotime($invoices_dt->end_date));
					} else {
						$start_date = strtotime($this->request->getData('start_date'));
					}
					// dd($start_date);
					// payment invoices Generate
					for ($i = 0; $i < $number_of_months; $i++) {
						$payment_invoices = $this->SupportPlansPayment->newEmptyEntity();
						$payment_invoices['plan_id'] = $id;
						$payment_invoices['project_id'] = $entity->project_id;
						// Calculate start and end dates for the current invoice
						if($this->request->getData('billing_frequency')=='Quarterly') {
							if($i==0) {
								$invoice_start_date = date('Y-m-d', strtotime("+$i months", $start_date));
							} else {
								$invoice_start_date = date('Y-m-d', strtotime("+1 months", $start_date));
							}
						} elseif($this->request->getData('billing_frequency')=='Yearly') {
							$invoice_start_date = date('Y-m-d', strtotime("+$i year", $start_date));
						}
						elseif($this->request->getData('billing_frequency')=='Monthly') {
							$invoice_start_date = date('Y-m-d', strtotime("+$i months", $start_date));
						}
						if($this->request->getData('billing_frequency')=='Quarterly') {
							$invoice_end_date = date('Y-m-d', strtotime("+4 months", strtotime($invoice_start_date)));
						} elseif($this->request->getData('billing_frequency')=='Yearly') {
							$invoice_end_date = date('Y-m-d', strtotime("+1 year", strtotime($invoice_start_date)));
						} elseif($this->request->getData('billing_frequency')=='Monthly') {
							$invoice_end_date = date('Y-m-d', strtotime("+1 months", strtotime($invoice_start_date)));
						}

						$payment_invoices['start_date'] = $invoice_start_date;
    					// $payment_invoices['end_date'] = $invoice_end_date;
						$invoice_end_date = date('Y-m-d', strtotime("-1 day", strtotime($invoice_end_date)));
						$payment_invoices['end_date'] = $invoice_end_date;
						$payment_invoices['amount'] = $this->request->getData('amount');
						$invoices_save = $this->SupportPlansPayment->save($payment_invoices);
						// Update start date for the next invoice
						if($this->request->getData('billing_frequency')=='Quarterly') {
							$start_date = strtotime("+3 months", strtotime($invoice_start_date));
						} 
						
					}
					// $this->Flash->success(__('Your Support Plan has been saved.'));
					// $this->clientNotification($email, $clientName, $pass);
					// return $this->redirect(['action' => 'supportPlans']);

					// end new plan
					$this->Flash->success('Update Details Successfully.');
					return $this->redirect(['action' => 'editSupportPlans', $id]);
				} else {
					$errors = $entity->getErrors();
					$this->Flash->error('Unable to update the record. Please check the form.');
				}
			}
			
			
				//End
				$this->set(compact('list', 'assignedList', 'id','payments','projectdata','client_list','plan_list'));
			}
			else
			{
				return $this->redirect($this->referer());
			}
	}

	public function statusChangeSupportPlan($id,$status)
	{
		$this->Authorization->skipAuthorization();
		// if($type=='delete') {
		// 	$val=1;
		// } else {
		// 	$val=0;
		// }
		 $query = $this->SupportPlan->updateQuery()
        ->set(['status' => $status])
        ->where(['id' => $id]);
		if ($query->execute())
			echo 1;
		else
			echo 0;
		die;
	}

	public function deleteSupportplan($id){
		$this->Authorization->skipAuthorization();
		
		$this->SupportPlan->updateQuery()
			->set(['deleted' => 1])
			->where(['id' => $id])
			->execute();
		echo 1;
		die();
	}

	public function invoiceStatusforPlans($id,$status,$type)
	{
		$this->Authorization->skipAuthorization();
		$data = $this->SupportPlansPayment->get($id);
		// dd($data);
		if($type=='invoice_sent') {
			if($data->invoice_sent==0) {
				$update = ['invoice_sent' => 1];
			} else {
				$update = ['invoice_sent' => 0];
			}
		} elseif($type=='invoice_paid') {
			if($data->invoice_paid==0) {
				$update = ['invoice_paid' => 1];
			} else {
				$update = ['invoice_paid' => 0];
			}
		}
		$this->SupportPlansPayment->updateQuery()
			->set($update)
			->where(['id' => $id])
			->execute();
		if ($query->execute())
			echo 1;
		else
			echo 0;
		die;
	}

}
<?php

use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Mvc\Controller;

$loader = new Loader();

$loader->registerDirs(
	array(
		__DIR__ .'/app/models/',
	)
)->register();

$di = new FactoryDefault();

$di->set('db',

function () {
		return new PdoMysql(
			array(
				"host"     => "localhost",
				"username" => "root",
				"password" => "furwadijaya97",
				"dbname"   => "guru_les",
			)
		);
	});

$app = new Micro($di);

$app['controllers'] = function() {
    return [
        'users' => true,
    ];
};

$app->get('/lession', function () use ($app) {

		$phql = "SELECT * FROM Subjects ORDER BY id_subject";
		$subjects = $app->modelsManager->executeQuery($phql);

		$data = array();
		foreach ($subjects as $subject) {
			$data[] = array(
				'id_subject'   => $subject->id_subject,
				'subject' => $subject->subject,
				'sub_subject' => $subject->sub_subject

			);
		}

		$redata = array(
			'area_id' => 'subjects',
			'items'   => $data,
		);

		echo json_encode($redata);
	});

$app->get('/login', function () use ($app) {
	$phql = "SELECT * FROM Users ORDER BY id_user";
	$logins = $app->modelsManager->executeQuery($phql);

	$data = array();
	foreach ($logins as $login) {
            $data[] = array(
                "id_user"   => $login->id_user,
                "email" => $login->email,
                "username" => $login->username,
                "password" => $login->password
            );
        }

        echo json_encode($data);
});

$app->get('/provinces', function () use ($app) {

		$phql = "SELECT * FROM Provinces WHERE id BETWEEN 31 AND 36";
		$provinces = $app->modelsManager->executeQuery($phql);

		$data = array();
		foreach ($provinces as $province) {
			$data[] = array(
				'id'   => $province->id,
				'name' => $province->name
			);
		}

		$redata = array(
			'area_id' => 'provinces',
			'items'   => $data,
		);

		echo json_encode($redata);
	});

$app->get('/provinces/search/{name}', function ($name) use ($app) {

		$phql = "SELECT * FROM Provinces WHERE name = :name:";
		$values = array('name' => $name);
		$province = $app->modelsManager->executeQuery($phql, $values)->getFirst();

		$data = array(
			'id'   => $province->id,
			'name' => $province->name
		);

		echo json_encode($data);
	});

$app->get('/getTeacher/{id:[0-9]+}', function ($id) use ($app) {

		$result = $app->modelsManager->createBuilder()
		    ->columns('full_name, birthday,address, PrfTeachers.city, provinces, districts, villages, primary_school, junior_high_school, senior_high_school, diploma, s1, s2, s3')
		    ->from('PrfTeachers')
		    ->innerJoin('EducationBackground', 'PrfTeachers.id_teachers = EducationBackground.teacher_id')
		    ->where("PrfTeachers.user_id = :id:", ["id" => $id])
		    ->getQuery()
		    ->execute()->getFirst();

		   $result1 = $app->modelsManager->createBuilder()
		    ->columns('radius_id, city, district')
		    ->from('RadiusLocations')
		    ->where("RadiusLocations.user_id = :id:", ["id" => $id])
		    ->getQuery()
		    ->execute();

		    $redata = array(
		    	'area_id'=>'profile',
			'education_history' => array(
				'full_name' => $result->full_name,
				'birthday' => $result->birthday,
				'address' => $result->address,
				'city' => $result->city,
				'provinces' => $result->provinces,
				'districts' => $result->districts,
				'villages' => $result->villages,
				'primary_school' => $result->primary_school,
				'junior_high_school' => $result->junior_high_school,
				'senior_high_school' => $result->senior_high_school,
				'diploma' => $result->diploma,
				's1' => $result->s1,
				's2' => $result->s2,
				's3' => $result->s3,
				),

			'radius_locations' => $result1
		);

		echo json_encode($redata);
	});




$app->get('/provinces/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT * FROM Provinces WHERE id = :id: ";
		$values = array('id' => $id);
		$province = $app->modelsManager->executeQuery($phql, $values)->getFirst();

		$response = new Response();

		if ($province == FALSE) {
			$response->setJsonContent(
				array(
					'status' => 'NOT-FOUND',
				)
			);
		} else {
			$response->setJsonContent(
				array(
					'status' => 'FOUND',
					'data'   => array(
						'id'    => $province->id,
						'name'  => $province->name
					)
				)
			);
		}

		return $response;
	});

// $app->get('/getUserHistory/{id:[0-9]+}', function ($id) use ($app) {

// 		$result = $app->modelsManager->createBuilder()
// 		    ->columns('id_transaction, id_teachers, user_id, full_name, birthday, gender, current_job, descriptions, address, city, provinces, districts, villages, dates, student_name, lession_name, times, packages_type, address_location, price_session, total_session, is_accept')
// 		    ->from('Transactions')
// 		    ->leftJoin('PrfTeachers', 'Transactions.id_prf_teacher = PrfTeachers.id_teachers')
// 		    ->where("Transactions.id_prf_student = :id:", array("id" => $id))
// 		    ->getQuery()
// 		    ->execute();

// 				$data = array();
// 				foreach ($result as $user) {
// 					$data[] = array(
// 						'id_transaction'    => $user->id_transaction,
// 						'id_teachers'       => $user->id_teachers,
// 						'user_id'        	=> $user->user_id,
// 						'full_name'         => $user->full_name,
// 						'birthday'        	=> $user->birthday,
// 						'gender'        	=> $user->gender,
// 						'current_job'       => $user->current_job,
// 						'descriptions'      => $user->descriptions,
// 						'address'        	=> $user->address,
// 						'city'        		=> $user->city,
// 						'provinces'        	=> $user->provinces,
// 						'districts'        	=> $user->districts,
// 						'villages'        	=> $user->villages,	
// 						'dates'        		=> $user->dates,	
// 						'student_name'      => $user->student_name,	
// 						'lession_name'      => $user->lession_name,	
// 						'times'        		=> $user->times,	
// 						'packages_type'     => $user->packages_type,	
// 						'address_location'  => $user->address_location,	
// 						'price_session'     => $user->price_session,	
// 						'total_session'     => $user->total_session,	
// 						'is_accept'        	=> $user->is_accept,
// 					);

// 				}

// 			$redata = array(
// 			'area_id' 	=> 'getUserHistory',
// 			'items'   	=> $data,
// 			);

// 		echo json_encode($redata);
// 	});

$app->get('/getUserHistory/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT t.*, pt.*, u.* FROM Transactions as t, PrfTeachers as pt, Users as u WHERE t.id_prf_teacher = pt.id_teachers AND t.id_prf_student=:id: AND pt.user_id=u.id_user order by t.id_transaction DESC";

		$values = array('id' => $id);

			$users = $app->modelsManager->executeQuery($phql, $values);

				$data = array();
				foreach ($users as $user) {
					$data[] = array(
						'id_transaction'    => $user->t->id_transaction,
						'id_teachers'       => $user->pt->id_teachers,
						'email'        	=> $user->u->email,
						'user_id'        	=> $user->pt->user_id,
						'full_name'         => $user->pt->full_name,
						'birthday'        	=> $user->pt->birthday,
						'gender'        	=> $user->pt->gender,
						'current_job'       => $user->pt->current_job,
						'descriptions'      => $user->pt->descriptions,
						'address'        	=> $user->pt->address,
						'city'        		=> $user->pt->city,
						'provinces'        	=> $user->pt->provinces,
						'districts'        	=> $user->pt->districts,
						'villages'        	=> $user->pt->villages,	
						'dates'        		=> $user->t->dates,	
						'student_name'      => $user->t->student_name,	
						'lession_name'      => $user->t->lession_name,	
						'times'        		=> $user->t->times,	
						'packages_type'     => $user->t->packages_type,	
						'address_location'  => $user->t->address_location,	
						'price_session'     => $user->t->price_session,	
						'total_session'     => $user->t->total_session,	
						'is_accept'        	=> $user->t->is_accept,	
					);
 
				}

			$redata = array(
			'area_id' => 'getUserHistory',
			'items'   	=> $data,
			);

		echo json_encode($redata);
	});

$app->get('/getTicketStudent/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT t.*, lt.*, u.* FROM Transactions as t, LearningTicket as lt, Users as u WHERE lt.id_transaction = t.id_transaction AND t.id_prf_teacher=:id: AND t.id_prf_student=u.id_user";

		$values = array('id' => $id);

			$users = $app->modelsManager->executeQuery($phql, $values);

				$data = array();
				foreach ($users as $user) {
					$data[] = array( 

						'id_ticket'    		   => $user->lt->id_ticket,
						'id_transaction'       => $user->t->id_transaction,
						'ticket_no'       	   => $user->lt->ticket_no,
						'created_at'       	   => $user->lt->created_at,
						'created_by'       	   => $user->lt->created_by,
						'id_prf_teacher'       => $user->t->id_prf_teacher,
						'id_prf_student'       => $user->t->id_prf_student,
						'email'       		   => $user->u->email,
						'dates'       		   => $user->t->dates,
						'student_name'         => $user->t->student_name,
						'lession_name'         => $user->t->lession_name,
						'times'         	   => $user->t->times,
						'status'       		   => $user->t->status,
						'packages_type'        => $user->t->packages_type,
						'address_location'     => $user->t->address_location,
						'price_session'        => $user->t->price_session,
						'total_session'        => $user->t->total_session,
						'is_accept'       	   => $user->t->is_accept,
						'feed_back'       	   => $user->t->feed_back,
					);
 
				}

			$redata = array(
			'area_id' => 'getTicketStudent',
			'items'   	=> $data,
			);

		echo json_encode($redata);

	});

// $app->get('/getTicketStudent/{id:[0-9]+}', function ($id) use ($app) {

// 		$result = $app->modelsManager->createBuilder()
// 		    ->columns('id_ticket, LearningTicket.id_transaction, ticket_no, created_at, created_by, id_prf_teacher, id_prf_student, dates, student_name, lession_name, times, status, packages_type, address_location, price_session, total_session, is_accept, feed_back')
// 		    ->from('LearningTicket')
// 		    ->leftJoin('Transactions', 'LearningTicket.id_transaction = Transactions.id_transaction')
// 		    ->where("Transactions.id_prf_teacher = :id:", array("id" => $id))
// 		    ->getQuery()
// 		    ->execute();

// 				$data = array();
// 				foreach ($result as $user) {
// 					$data[] = array(
// 						'id_ticket'    		   => $user->id_ticket,
// 						'id_transaction'       => $user->id_transaction,
// 						'ticket_no'       	   => $user->ticket_no,
// 						'created_at'       	   => $user->created_at,
// 						'created_by'       	   => $user->created_by,
// 						'id_prf_teacher'       => $user->id_prf_teacher,
// 						'id_prf_student'       => $user->id_prf_student,
// 						'dates'       		   => $user->dates,
// 						'student_name'         => $user->student_name,
// 						'lession_name'         => $user->lession_name,
// 						'times'         	   => $user->times,
// 						'status'       		   => $user->status,
// 						'packages_type'        => $user->packages_type,
// 						'address_location'     => $user->address_location,
// 						'price_session'        => $user->price_session,
// 						'total_session'        => $user->total_session,
// 						'is_accept'       	   => $user->is_accept,
// 						'feed_back'       	   => $user->feed_back,
// 					);

// 				}

// 			$redata = array(
// 			'area_id' 	=> 'getTicketStudent',
// 			'items'   	=> $data,
// 			);

// 		echo json_encode($redata);
// 	});

$app->get('/getUserRating/{id:[0-9]+}', function ($id) use ($app) {

		$datad = $app->modelsManager->createBuilder()
		    ->columns('id_transaction, teacher_id, id, id_teachers, user_id, full_name, birthday, gender, current_job, descriptions, address, city, provinces, villages, districts, rating,rating_date, rating_comments')
		    ->from('Transactions')
		    ->leftJoin('Rating', 'Transactions.id_prf_teacher = Rating.teacher_id')
   			->join('PrfTeachers', 'Transactions.id_prf_teacher=PrfTeachers.id_teachers')
		    ->where("Transactions.id_prf_student = :id: GROUP BY Transactions.is_accept HAVING(Transactions.is_accept) = 'Y'", array("id" => $id))
		    ->getQuery()
		    ->execute();

				$data = array();
				foreach ($datad as $user) {
					$data[] = array(
						'id_transaction'    => $user->id_transaction,
						'id_teachers'       => $user->id_teachers,
						'user_id'        	=> $user->user_id,
						'full_name'         => $user->full_name,
						'birthday'        	=> $user->birthday,
						'gender'        	=> $user->gender,
						'current_job'       => $user->current_job,
						'descriptions'      => $user->descriptions,
						'address'        	=> $user->address,
						'city'        		=> $user->city,
						'provinces'        	=> $user->provinces,
						'districts'        	=> $user->districts,
						'villages'        	=> $user->villages,	
						'rating'			=>	array(
							'id'        		=> $user->id,
							'teacher_id' 		=> $user->teacher_id,
							'rating'        	=> $user->rating,
							'rating_date'       => $user->rating_date,
							'rating_comments'   => $user->rating_comments,
						)
					);
				}

			$redata = array(
			'area_id' 	=> 'getRating',
			'items'   	=> $data,
			);

		echo json_encode($redata);
	});

$app->get('/getOnRating/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT t.*, r.* FROM PrfTeachers as t, PrfStudents as s, Rating as r  WHERE r.student_id=s.user_id AND t.user_id = r.teacher_id  AND r.student_id=:id:";

		$values = array('id' => $id);

			$users = $app->modelsManager->executeQuery($phql, $values);

				$data = array();
				foreach ($users as $user) {
					$data[] = array(
						'id_teachers'       => $user->t->id_teachers,
						'user_id'        	=> $user->t->user_id,
						'full_name'         => $user->t->full_name,
						'birthday'        	=> $user->t->birthday,
						'gender'        	=> $user->t->gender,
						'current_job'       => $user->t->current_job,
						'descriptions'      => $user->t->descriptions,
						'address'        	=> $user->t->address,
						'city'        		=> $user->t->city,
						'provinces'        	=> $user->t->provinces,
						'districts'        	=> $user->t->districts,
						'villages'        	=> $user->t->villages,	
						'rating'			=>	array(
							'teacher_id' 		=> $user->r->teacher_id,
							'student_id'        => $user->r->student_id,
							'rating'        	=> $user->r->rating,
							'rating_date'       => $user->r->rating_date,
							'rating_comments'   => $user->r->rating_comments,
						)
					);

				}

			$redata = array(
			'area_id' 	=> 'getRating',
			'items'   	=> $data,
			);

		echo json_encode($redata);
	});

//
$app->get('/getRating/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT t.*, r.* FROM PrfTeachers as t, PrfStudents as s, Rating as r  WHERE r.student_id=s.user_id AND t.user_id = r.teacher_id  AND r.student_id=:id:";

		$values = array('id' => $id);

			$users = $app->modelsManager->executeQuery($phql, $values);

				$data = array();
				foreach ($users as $user) {
					$data = array(
						'rating'			=> $data,
						'id_teachers'       => $user->t->id_teachers,
						'user_id'        	=> $user->t->user_id,
						'full_name'         => $user->t->full_name,
						'birthday'        	=> $user->t->birthday,
						'gender'        	=> $user->t->gender,
						'current_job'       => $user->t->current_job,
						'descriptions'      => $user->t->descriptions,
						'address'        	=> $user->t->address,
						'city'        		=> $user->t->city,
						'provinces'        	=> $user->t->provinces,
						'districts'        	=> $user->t->districts,
						'villages'        	=> $user->t->villages,			
					);

					$data = array(
						'teacher_id' 		=> $user->r->teacher_id,
						'student_id'        => $user->r->student_id,
						'rating'        	=> $user->r->rating,
						'rating_date'       => $user->r->rating_date,
						'rating_comments'   => $user->r->rating_comments,
					);
				}

			$redata = array(
			'area_id' 	=> 'getRating',
			'items'   	=> [$data],
			);

		echo json_encode($redata);
	});

$app->delete('/provinces/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "DELETE FROM Provinces WHERE id = :id:";

		$values = array(
			'id' => $id,
		);

		$results = $app->modelsManager->executeQuery($phql, $values);

		$response = new Response();

		if ($results->success() == TRUE) {

			$response->setStatusCode(200, "Deleted");

			$response->setJsonContent(
				array(
					'status' => 'Deleted',
				)
			);
		} else {

			$response->setStatusCode(409, "Conflict");

			$errors = array();
			foreach ($results->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				array(
					'status'   => 'ERROR',
					'messages' => $errors,
				)
			);
		}

		return $response;
	});

$app->get('/regencies/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT * FROM Regencies WHERE province_id = :id: ORDER BY name DESC";
		$values = array('id' => $id);
		$regencies = $app->modelsManager->executeQuery($phql, $values);

		$data = array();
		foreach ($regencies as $regencies) {
			$data[] = array(
				'id'          => $regencies->id,
				'province_id' => $regencies->province_id,
				'name'        => $regencies->name
			);
		}

		$redata = array(
			'area_id' => 'regencies',
			'items'   => $data,
		);

		// third
		echo json_encode($redata);
	});

$app->get('/getSchedules/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT * FROM Schedules WHERE user_id = :id:";
		$values = array('id' => $id);
		$regencies = $app->modelsManager->executeQuery($phql, $values);

		$data = array();
		foreach ($regencies as $regencies) {
			$data[] = array(
				'schedule_id'           => $regencies->schedule_id,
				'user_id' 				=> $regencies->user_id,
				'schedule_days'         => $regencies->schedule_days,
				'schedule_times'        => $regencies->schedule_times
			);
		}

		$redata = array(
			'area_id' => 'Schedules',
			'items'   => $data,
		);

		// third
		echo json_encode($redata);
	});

// Searches for districts with regency_id in their name
$app->get('/districts/{id:[0-9]+}', function ($id) use ($app) {
		// first
		$phql = "SELECT * FROM Districts WHERE regency_id = :id:";
		$values = array('id' => $id);
		$districts = $app->modelsManager->executeQuery($phql, $values);

		// second
		$data = array();
		foreach ($districts as $district) {
			$data[] = array(
				'id'         => $district->id,
				'regency_id' => $district->regency_id,
				'name'       => $district->name
			);
		}

		$redata = array(
			'area_id' => 'districts',
			'items'   => $data,
		);

		// third
		echo json_encode($redata);
	});

$app->get('/getTransactionByStudent/{id:[0-9]+}', function ($id) use ($app) {

		$data = $app->modelsManager->createBuilder()
		    ->columns('id_transaction, id_prf_teacher, id_prf_student, dates, student_name, lession_name, times,  status, packages_type, address_location, price_session, total_session, is_accept, full_name, birthday, gender, current_job, descriptions, address, city, provinces, districts, villages')
		    ->from('Transactions')
		    ->innerJoin('PrfTeachers', 'Transactions.id_transaction	 = PrfTeachers.id_teachers')
		    ->where("Transactions.id_prf_student = :id: AND is_accept = 'Y'", ["id" => $id])
		    ->getQuery()
		    ->execute();

		    $redata = array(
			'area_id' => 'Transactions',
			'items'   => $data,
		);

		echo json_encode($redata);
	});

// OLD API TRANSACTION
//$app->get('/getTransactionByTeacher/{id:[0-9]+}', function ($id) use ($app) {

// 		$data = $app->modelsManager->createBuilder()
// 		    ->columns('id_transaction, id_prf_teacher, id_prf_student, dates, student_name, lession_name, times, status, packages_type, address_location, price_session, total_session, is_accept, full_name, birthday, gender, descriptions, address, city, provinces, districts, villages')
// 		    ->from('Transactions')
// 		    ->innerJoin('PrfStudents', 'Transactions.id_prf_student	 = PrfStudents.id_students')
// 		    ->where("Transactions.id_prf_teacher = :id:", ["id" => $id])
// 		    ->getQuery()
// 		    ->execute();

// 		    $redata = array(
// 			'area_id' => 'Transactions',
// 			'items'   => $data,
// 		);

// 		echo json_encode($redata);
// 	});
$app->get('/getHonorByID/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT sum(price_session) as honor,* FROM Transactions WHERE id_prf_teacher = :id:";
		$values = array('id' => $id);
		$results = $app->modelsManager->executeQuery($phql, $values);

		$data = array();
		foreach ($results as $result) {
			$data = $result->honor; 
		}

		$redata = array(
			'area_id' => 'getHonor',
			'honor'   => $data,
		);

		echo json_encode($redata);
	});

$app->get('/getTransactionByTeacher/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT t.*, ps.*, u.* FROM Transactions as t, PrfStudents as ps, Users as u WHERE t.id_prf_student = ps.id_students AND t.id_prf_teacher=:id: AND ps.user_id=u.id_user order by t.id_transaction DESC";

		$values = array('id' => $id);

			$users = $app->modelsManager->executeQuery($phql, $values);

				$data = array();
				foreach ($users as $user) {
					$data[] = array(
						'id_transaction' => $user->t->id_transaction,
						'id_prf_teacher' => $user->t->id_prf_teacher,
						'id_prf_student' => $user->t->id_prf_student,
						'dates'          => $user->t->dates,
						'email'          => $user->u->email,
						'student_name'   => $user->t->student_name,
						'lession_name'   => $user->t->lession_name,
						'times'          => $user->t->times,
						'status'         => $user->t->status,
						'packages_type'  => $user->t->packages_type,
						'address_location'=> $user->t->address_location,
						'price_session'   => $user->t->price_session,
						'total_session'   => $user->t->total_session,
						'is_accept'       => $user->t->is_accept,
						'full_name'       => $user->ps->full_name,
						'birthday'        => $user->ps->birthday,
						'gender'          => $user->ps->gender,
						'descriptions'    => $user->ps->descriptions,
						'address'         => $user->ps->address,
						'city'        	  => $user->ps->city,
						'address'         => $user->ps->address,
						'provinces'       => $user->ps->provinces,
						'districts'       => $user->ps->districts,
						'villages'        => $user->ps->villages,			
					);
 
				}

			$redata = array(
			'area_id' => 'Transactions',
			'items'   	=> $data,
			);

		echo json_encode($redata);
	});

$app->get('/districts/search/{name}', function ($name) use ($app) {

		$phql = "SELECT * FROM Districts WHERE name = :name: ";
		$values = array('name' => $name);
		$dist = $app->modelsManager->executeQuery($phql, $values)->getFirst();

		$data = array(
			'id'         => $dist->id,
			'regency_id' => $dist->regency_id,
			'name'       => $dist->name
		);

		echo json_encode($data);
	});

// Searches for villages with district_id in their name
$app->get('/villages/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT * FROM Villages WHERE district_id = :id:";
		$values = array('id' => $id);
		$villages = $app->modelsManager->executeQuery($phql, $values);

		$data = array();
		foreach ($villages as $village) {
			$data[] = array(
				'id'          => $village->district_id,
				'district_id' => $village->district_id,
				'name'        => $village->name
			);
		}

		$redata = array(
			'area_id' => 'villages',
			'items'   => $data,
		);

		echo json_encode($redata);
	});

$app->get('/getEducation/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT * FROM EducationBackground WHERE id = :id:";
		$values = array('id' => $id);
		$results = $app->modelsManager->executeQuery($phql, $values);

		$data = array();
		foreach ($results as $result) {
			$data[] = array(
				'id'          				=> $result->id,
				'teacher_id' 				=> $result->teacher_id,
				'primary_school'        	=> $result->primary_school,
				'junior_high_school'        => $result->junior_high_school,
				'senior_high_school'        => $result->senior_high_school,
				'diploma'        			=> $result->diploma,
				's1'        				=> $result->s1,
				's2'        				=> $result->s2,
				's3'        				=> $result->s3,
			);
		}

		$redata = array(
			'area_id' => 'EducationBackground',
			'items'   => $data,
		);

		echo json_encode($redata);
	});

$app->get('/getRadius/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT * FROM RadiusLocations WHERE user_id = :id:";
		$values = array('id' => $id);
		$results = $app->modelsManager->executeQuery($phql, $values);

		$data = array();
		foreach ($results as $result) {
			$data[] = array(
				'radius_id'   => $result->radius_id,
				'user_id'     => $result->user_id,
				'city'        => $result->city,
				'district'    => $result->district,
			);
		}

		$redata = array(
			'area_id' => 'getRadiusLocation',
			'items'   => $data,
		);

		echo json_encode($redata);
	});

$app->get('/getTicket/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT * FROM LearningTicket WHERE id_ticket = :id: order by id_ticket DESC";
		$values = array('id' => $id);
		$results = $app->modelsManager->executeQuery($phql, $values);

		$data = array();
		foreach ($results as $result) {
			$data[] = array(
				'id_transaction'   => $result->id_transaction,
				'ticket_no'     => $result->ticket_no,
				'created_at'        => $result->created_at,
				'created_by'    => $result->created_by,
			);
		}

		$redata = array(
			'area_id' => 'getTicket',
			'items'   => $data,
		);

		echo json_encode($redata);
	});

$app->get('/users', function () use ($app) {

		$phql = "SELECT * FROM Users ORDER BY id_user";
		$users = $app->modelsManager->executeQuery($phql);

		$data = array();
		foreach ($users as $user) {
			$data[] = array(
				'id_user'    => $user->id_user,
				'email'      => $user->email,
				'username'   => $user->username,
				'password'   => $user->password,
				'created_by' => $user->created_by,
				'created_at' => $user->created_at,
				'updated_by' => $user->updated_by,
				'updated_at' => $user->updated_at
			);
		}

		$redata = array(
			'area_id' => 'users',
			'items'   => $data,
		);

		echo json_encode($redata);
	});

$app->post('/register', function () use ($app) {

		$phql = "INSERT INTO Users (id_user, uid, email, username, password, account_type) VALUES (NULL, :uid:, :email:, :username:, :password:, :account_type:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'uid'      	 	 => uniqid(rand(100, 1000000)),
				'email'      	 => $_POST['email'],
				'username'   	 => $_POST['username'],
				'password'   	 => md5($_POST['password']),
				'account_type'   => $_POST['account_type'],
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Registered"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
	});

$app->post('/inputRating', function () use ($app) {

		$phql = "INSERT INTO Rating (teacher_id, student_id, rating, rating_date, rating_comments) VALUES (:teacher_id:, :student_id:, :rating:, :rating_date:, :rating_comments:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'teacher_id'      	 => $_POST['teacher_id'],
				'student_id'   	 	 => $_POST['student_id'],
				'rating'   	 		 => $_POST['rating'],
				'rating_date'   	 => $_POST['rating_date'],
				'rating_comments' 	 => $_POST['rating_comments'],
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Input Success"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
	});

$app->post('/inputReport', function () use ($app) {

		$phql = "INSERT INTO LearningReport (id_ticket, feed_back) VALUES (:id_ticket:, :feed_back:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'id_ticket'      	 => $_POST['id_ticket'],
				'feed_back'   	 	 => $_POST['feed_back'],
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Input Success"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
	});

$app->post('/inputSchedules', function () use ($app) {

		$phql = "INSERT INTO Schedules (user_id, schedule_days, schedule_times) VALUES (:user_id:, :schedule_days:, :schedule_times:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'user_id'      	 => $_POST['user_id'],
				'schedule_days'  => $_POST['schedule_days'],
				'schedule_times' => $_POST['schedule_times'],
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Input Success"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
	});

$app->delete('/deleteScheduleID/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "DELETE FROM Schedules WHERE schedule_id = :id:";

		$values = array(
			'id' => $id,
		);

		$results = $app->modelsManager->executeQuery($phql, $values);

		$response = new Response();

		if ($results->success() == TRUE) {

			$response->setStatusCode(200, "Deleted");

			$response->setJsonContent(
				array(
					'status' => 'Deleted',
				)
			);
		} else {

			$response->setStatusCode(409, "Conflict");

			$errors = array();
			foreach ($results->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				array(
					'status'   => 'ERROR',
					'messages' => $errors,
				)
			);
		}

		return $response;
	});

$app->post('/uploadAction', function ( ) use ( $app ) {

	if (!$this->request->hasFiles() == @$_FILES['name']) {

		die('ERROR');

	} else {

		$uploads = $this->request->getUploadedFiles();
		$isUploaded = false;

		foreach ($uploads as $upload) {
			$path = md5(uniqid(rand(), true)).strtolower($upload->getname());

			($upload->moveTo($path)) ? $isUploaded = true : $isUploaded = false;
		}

		($isUploaded) ? die("File Berhasil Diupload") : die("Upload Error");
	}
});

// $app->post('/uploadAction', function ( ) use ( $app ) {

// 	if($this->request->hasFiles() == true){

// 	 $uploads = $this->request->getUploadedFiles();
// 	 $isUploaded = false;

// 	 foreach($uploads as $upload){
// 	 $path = 'temp/'.md5(uniqid(rand(), true)).’-’.strtolower($upload->getname());
// 	 ($upload->moveTo($path)) ? $isUploaded = true : $isUploaded = false;
// 	 }
// 	 ($isUploaded) ? die('Files successfully uploaded.') : die('Some error ocurred.');
// 	}else{
// 	 die('You must choose at least one file to send. Please try again.');
// 	}
// });


$app->post('/loginAction', function ( ) use ( $app ) {

		$post = $this->request->getPost();

		$username = $post['username'];
		$password = $post['password'];

		//$phql = "SELECT * FROM Users WHERE username=:username: AND password=:password: AND account_type = '1'";

		$phql = "SELECT ps.*, u.* FROM Users as u, PrfStudents as ps WHERE u.username=:username: AND u.password=:password: AND account_type = '1' AND ps.user_id=u.id_user";

		$result = $app->modelsManager->executeQuery($phql,
			[
				'username' => $username,
				'password'=>  md5($password),
			]
		);

		$data = array();
			foreach ($result as $results) {
				$data = array(

					'code' 			=> 1,
					'status' 		=> 'success',
					'id_user'		=> $results->u->id_user,
					'id_student'=> $results->ps->id_students,
					'uid'   		=> $results->u->uid,
					'email'   		=> $results->u->email,
					'username'   	=> $results->u->username,
					'account_type'  => $results->u->account_type,
					'created_by'   	=> $results->u->created_by,
					'updated_at'   	=> $results->u->updated_at,
					'updated_by'   	=> $results->u->updated_by,
				);
			}

		$response = new Response();

	   if (count($result)>=1) {
	   		$response->setStatusCode(200, "OK");

				$response->setJsonContent(
					array(
						'data' => $data
					)
				);
	   } else {

			$response->setJsonContent(
				array(
						'data' => array(

					'code' => 2,
					'status' => 'failed',)
				)
			);
		}
	return $response;
});

$app->post('/loginGuru', function ( ) use ( $app ) {

		$post = $this->request->getPost();

		$username = $post['username'];
		$password = $post['password'];

		$phql = "SELECT pt.*, u.* FROM Users as u, PrfTeachers as pt WHERE u.username=:username: AND u.password=:password: AND account_type = '0' AND pt.user_id=u.id_user";

		$result = $app->modelsManager->executeQuery($phql,
			[
				'username' => $username,
				'password'=>  md5($password),
			]
		);

		$data = array();
			foreach ($result as $results) {
				$data = array(

					'code' 			=> 1,
					'status' 		=> 'success',
					'id_user'		=> $results->u->id_user,
					'id_teacher'=> $results->pt->id_teachers,
					'uid'   		=> $results->u->uid,
					'email'   		=> $results->u->email,
					'username'   	=> $results->u->username,
					'account_type'  => $results->u->account_type,
					'created_by'   	=> $results->u->created_by,
					'updated_at'   	=> $results->u->updated_at,
					'updated_by'   	=> $results->u->updated_by,
				);
			}

		$response = new Response();

	   if (count($result)>=1) {
	   		$response->setStatusCode(200, "OK");

				$response->setJsonContent(
					array(
						'data' => $data
					)
				);
	   } else {

			$response->setJsonContent(
				array(
						'data' => array(

					'code' => 2,
					'status' => 'failed',)
				)
			);
		}
	return $response;
});

$app->post('/transaction', function () use ($app){

	$phql = "INSERT INTO Transactions (id_transaction, id_prf_teacher, id_prf_student, dates, student_name, lession_name, times ,status, packages_type, address_location, price_session, total_session, is_accept) VALUES ( NULL, :id_prf_teacher:, :id_prf_student:, :dates:, :student_name:, :lession_name:, :times:, :status:, :packages_type:, :address_location:, :price_session:, :total_session:, :is_accept:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'id_prf_teacher'   	 	 => $_POST['id_prf_teacher'],
				'id_prf_student'   	 	 => $_POST['id_prf_student'],
				'dates'   	 	 		 => $_POST['dates'],
				'student_name'   	 	 => $_POST['student_name'],
				'lession_name'   	 	 => $_POST['lession_name'],
				'times'   	 	 		 => $_POST['times'],
				'status'   				 => $_POST['status'],
				'packages_type' 	 	 => $_POST['packages_type'],
				'address_location' 	 	 => $_POST['address_location'],
				'price_session' 	 	 => $_POST['price_session'],
				'total_session' 	 	 => $_POST['total_session'],
				'is_accept' 	 	 	 => $_POST['is_accept']
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Success Insert to Transaction"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
});

$app->post('/inputTicket', function () use ($app){

	$phql = "INSERT INTO LearningTicket (id_ticket, id_transaction, ticket_no, created_at, created_by) VALUES ( NULL, :id_transaction:, :ticket_no:, :created_at:, :created_by:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'id_transaction'   	 => $_POST['id_transaction'],
				'ticket_no'   	 	 => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8),
				'created_at'   	 	 => $_POST['created_at'],
				'created_by'   		 => $_POST['created_by'],
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Success Insert to LearningTicket"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
});

$app->post('/inbox', function () use ($app){

	$phql = "INSERT INTO Inbox (id_inbox, user_id, title_type, content, is_read, status, created_at, created_by) VALUES (NULL, :user_id:, :title_type:, :content:, :is_read:, :status:, :created_at:, :created_by:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'user_id'   	 	 => $_POST['user_id'],
				'title_type'   	 	 => $_POST['title_type'],
				'content'   	 	 => $_POST['content'],
				'is_read'   	 	 => "N",
				'status'   			 => "inbox",
				'created_at' 	 	 => date("Y-m-d H:i:s"),
				'created_by' 	 	 => "1"
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Success Insert to Inbox"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
});


$app->post('/editInboxByID/{id:[0-9]+}', function ($id) use ($app){

	$phql = "UPDATE Inbox SET is_read = :is_read: WHERE id_inbox = :id:";

		$status = $app->modelsManager->executeQuery($phql, array(
				'is_read'    => $_POST['is_read'],
				'id'   	 	 => $id
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Success Edit Inbox"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
});


$app->get('/getInboxByID/{id:[0-9]+}', function ($id) use ($app) {

		$phql = "SELECT * FROM Inbox WHERE user_id = :id: ORDER BY id_inbox DESC";
				$values = array('id' => $id);

		$subjects = $app->modelsManager->executeQuery($phql,$values);

			$data = array();
			foreach ($subjects as $subject) {
				$data[] = array(
					'id_inbox'   	=> $subject->id_inbox,
					'user_id'		=> $subject->user_id,
					'title_type'	=> $subject->title_type,
					'content'       => $subject->content,
					'status'   		=> $subject->status,
					'created_at'   	=> $subject->created_at,
					'created_by' 	=> $subject->created_by

				);
			}

			$redata = array(
				'data_id' => 'list_inbox',
				'items'   => $data,
			);

			echo json_encode($redata);
	});

$app->post('/inputStudent', function () use ($app){

	$phql = "INSERT INTO PrfStudents (id_students, user_id, full_name, birthday, gender, descriptions, address, city, provinces, districts, villages) VALUES (NULL, :user_id:, :user_id:, :birthday:, :gender:, :descriptions:, :address:, :city:, :provinces:, :districts:, :villages:)";

	$status = $app->modelsManager->executeQuery($phql,
		[
			'user_id' 	   => $_POST['user_id'],
			'full_name'    => $_POST['full_name'],
			'birthday' 	   => $_POST['birthday'],
			'gender' 	   => $_POST['gender'],
			'descriptions' => $_POST['descriptions'],
			'address' 	   => $_POST['address'],
			'city' 		   => $_POST['city'],
			'provinces'    => $_POST['provinces'],
			'districts'    => $_POST['districts'],
			'villages'     => $_POST['villages'],
		]
	);

	$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Success Insert to  PrfStudents"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
});

$app->post('/insertEducation', function () use ($app){

	$phql = "INSERT INTO EducationBackground (id, teacher_id, primary_school, junior_high_school, senior_high_school, diploma, s1, s2,s3) VALUES ( :id:, :teacher_id:, :primary_school:, :junior_high_school:, :senior_high_school:, :diploma:, :s1:, :s2:, :s3:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'id'      	 		 => $_POST['id'],
				'teacher_id' 		 => $_POST['teacher_id'],
				'primary_school'   	 => $_POST['primary_school'],
				'junior_high_school' => $_POST['junior_high_school'],
				'senior_high_school' => $_POST['senior_high_school'],
				'diploma' 	 	 	 => $_POST['diploma'],
				's1' 	 	 	 	 => $_POST['s1'],
				's2' 	 	 	 	 => $_POST['s2'],
				's3' 	 	 	 	 => $_POST['s3']
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Success Insert to EducationBackground"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
});

$app->post('/inputRadius', function () use ($app){

	$phql = "INSERT INTO RadiusLocations (user_id, city, district) VALUES ( :user_id:, :city:, :district:)";

		$status = $app->modelsManager->executeQuery($phql, array(
				'user_id'    => $_POST['user_id'],
				'city' 		 => $_POST['city'],
				'district'   => $_POST['district'],
			));

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Success Insert to RadiusLocations"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
});

$app->post("/editEducation/{id:[0-9]+}", function ($id) use ($app) {

		$phql = "UPDATE EducationBackground SET teacher_id = :teacher_id:, primary_school = :primary_school:, junior_high_school = :junior_high_school:, senior_high_school = :senior_high_school:, diploma = :diploma:, s1 = :s1:, s2 = :s2:, s3 = :s3: WHERE id = :id:";

		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'id'      	 		 => $id,
				'teacher_id' 		 => $_POST['teacher_id'],
				'primary_school'   	 => $_POST['primary_school'],
				'junior_high_school' => $_POST['junior_high_school'],
				'senior_high_school' => $_POST['senior_high_school'],
				'diploma' 	 	 	 => $_POST['diploma'],
				's1' 	 	 	 	 => $_POST['s1'],
				's2' 	 	 	 	 => $_POST['s2'],
				's3' 	 	 	 	 => $_POST['s3'],
			]
		);

		 $response = new Response();

        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    "status" => "OK"
                ]
            );
        } else {
            $response->setStatusCode(409, "Conflict");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status"   => "ERROR",
                    "messages" => $errors,
                ]
            );
        }

        return $response;
	}
);

$app->post("/editTeacher/{id:[0-9]+}", function ($id) use ($app){

	$phql = "UPDATE PrfTeachers SET full_name = :full_name:, birthday = :birthday:, gender = :gender:, current_job = :current_job:, descriptions = :descriptions:, address = :address:, city = :city:, provinces = :provinces:, districts = :districts:, villages = :villages: WHERE user_id = :id:";


	$status = $app->modelsManager->executeQuery($phql,
		array(
			'id' => $id,
			'full_name' => $_POST['full_name'],
			'birthday' => $_POST['birthday'],
			'gender' => $_POST['gender'],
			'current_job' => $_POST['current_job'],
			'descriptions' => $_POST['descriptions'],
			'address' => $_POST['address'],
			'city' => $_POST['city'],
			'provinces' => $_POST['provinces'],
			'districts' => $_POST['districts'],
			'villages' => $_POST['villages'],
		)
	);

	$phql1 = "UPDATE EducationBackground SET primary_school = :primary_school:, junior_high_school = :junior_high_school:, senior_high_school = :senior_high_school:, diploma = :diploma:, s1 = :s1:, s2 = :s2:, s3 = :s3: WHERE id = :id:";

		$statusi = $app->modelsManager->executeQuery(
			$phql1,
			[
				'id'      	 		 => $id,
				'primary_school'   	 => $_POST['primary_school'],
				'junior_high_school' => $_POST['junior_high_school'],
				'senior_high_school' => $_POST['senior_high_school'],
				'diploma' 	 	 	 => $_POST['diploma'],
				's1' 	 	 	 	 => $_POST['s1'],
				's2' 	 	 	 	 => $_POST['s2'],
				's3' 	 	 	 	 => $_POST['s3'],
			]
		);

		$response = new Response();

        if ($status->success() === true AND $statusi->success() === true) {
            $response->setJsonContent(
                [
                    "status" => "OK"
                ]
            );
        } else {
            $response->setStatusCode(409, "Conflict");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status"   => "ERROR",
                    "messages" => $errors,
                ]
            );
        }

        return $response;
});

$app->post("/editRadius/{id:[0-9]+}", function ($id) use ($app) {

		$phql = "UPDATE RadiusLocations SET city = :city:, district = :district: WHERE radius_id = :id:";

		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'id'    	 => $id,
				'city' 		 => $_POST['city'],
				'district'   => $_POST['district'],
			]
		);

		 $response =new Response();;

        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    "status" => "OK"
                ]
            );
        } else {
            $response->setStatusCode(409, "Conflict");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status"   => "ERROR",
                    "messages" => $errors,
                ]
            );
        }

        return $response;
	}
);

$app->post("/editTransaction/{id:[0-9]+}", function ($id) use ($app) {

	$phql = "UPDATE Transactions SET id_prf_teacher = :id_prf_teacher:, id_prf_student = :id_prf_student:, dates = :dates:, student_name = :student_name:, lession_name = :lession_name:, times = :times:, status = :status:, packages_type = :packages_type:, address_location = :address_location:, price_session = :price_session:, total_session = :total_session:, is_accept = :is_accept: WHERE id_transaction = :id:";

	$status = $app->modelsManager->executeQuery($phql, array(

				'id'		 			 => $id,
				'id_prf_teacher'   	 	 => $_POST['id_prf_teacher'],
				'id_prf_student'   	 	 => $_POST['id_prf_student'],
				'dates'   	 	 		 => $_POST['dates'],
				'student_name'   	 	 => $_POST['student_name'],
				'lession_name'   	 	 => $_POST['lession_name'],
				'times'   	 	 		 => $_POST['times'],
				'status'   				 => $_POST['status'],
				'packages_type' 	 	 => $_POST['packages_type'],
				'address_location' 	 	 => $_POST['address_location'],
				'price_session' 	 	 => $_POST['price_session'],
				'total_session' 	 	 => $_POST['total_session'],
				'is_accept' 	 	 	 => $_POST['is_accept'],
			));

		$response = new Response();

        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    "status" => "OK"
                ]
            );
        } else {
            $response->setStatusCode(409, "Conflict");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status"   => "ERROR",
                    "messages" => $errors,
                ]
            );
        }

        return $response;
});

$app->post("/setTransactionStatusD/{id:[0-9]+}", function ($id) use ($app) {

	$phql = "UPDATE Transactions SET is_accept = :is_accept: WHERE id_transaction = :id:";

	$status = $app->modelsManager->executeQuery($phql, array(

				'id'		 			 => $id,
				'is_accept' 	 	 	 => $_POST['is_accept'],
			));

		$response = new Response();

        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    "status" => "OK"
                ]
            );
        } else {
            $response->setStatusCode(409, "Conflict");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status"   => "ERROR",
                    "messages" => $errors,
                ]
            );
        }

        return $response;
});

$app->post("/setTransactionStatus/{id:[0-9]+}", function ($id) use ($app) {

	$phql = "UPDATE Transactions SET is_accept = :is_accept: WHERE id_transaction = :id:";

	$status = $app->modelsManager->executeQuery($phql, array(

				'id'		 			 => $id,
				'is_accept' 	 	 	 => $_POST['is_accept'],
			));

	$phqlx = "INSERT INTO LearningTicket (id_transaction, ticket_no, created_at, created_by) VALUES ( :id_transaction:, :ticket_no:, :created_at:, :created_by:)";

	$statusx = $app->modelsManager->executeQuery($phqlx, array(

				'id_transaction' 	 	 => $_POST['id_transaction'],
				'ticket_no' 	 	 	 => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8),
				'created_at' 	 	 	 => date("Y-m-d H:i:s"),
				'created_by' 	 	 	 => "1",
			));

		$response = new Response();

        if ($status->success() === true AND $statusx->success() === true) {
            $response->setJsonContent(
                [
                    "status" => "OK"
                ]
            );
        } else {
            $response->setStatusCode(409, "Conflict");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status"   => "ERROR",
                    "messages" => $errors,
                ]
            );
        }

        return $response;
});

$app->post("/editRating/{id:[0-9]+}", function ($id) use ($app) {

		$phql = "UPDATE Rating SET teacher_id = :teacher_id:, student_id = :student_id:, rating = :rating:, rating_date = :rating_date:, rating_comments = :rating_comments: WHERE id = :id:";

		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'id'      	 		 => $id,
				'teacher_id' 		 => $_POST['teacher_id'],
				'student_id'   	 	 => $_POST['student_id'],
				'rating' 			 => $_POST['rating'],
				'rating_date' 		 => $_POST['rating_date'],
				'rating_comments' 	 => $_POST['rating_comments'],
			]
		);

		 $response = new Response();

        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    "status" => "OK"
                ]
            );
        } else {
            $response->setStatusCode(409, "Conflict");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status"   => "ERROR",
                    "messages" => $errors,
                ]
            );
        }

        return $response;
	}
);

$app->post("/userUpdate/{id:[0-9]+}", function ($id) use ($app) {

		$phql = "UPDATE Users SET uid = :uid:, email = :email:, username = :username:, password = :password:, account_type = :account_type:, created_by = :created_by:, updated_by = :updated_by: WHERE id_user = :id_user:";

		
		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'id_user'    	 => $id,
				'uid'  		 	 => uniqid($id),
				'email'       	 => $_POST['email'],
				'username'   	 => $_POST['username'],
				'password'   	 => md5($_POST['password']),
				'account_type'   => $_POST['account_type'],
				'created_by' 	 => $_POST['created_by'],
				'updated_by'  	 => $_POST['updated_by'],
			]
		);

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Updated"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
	}
);

$app->get('/getTeacherId/{id:[0-9]}', function ($id) use ($app) {

		$phql = "SELECT * FROM PrfTeachers WHERE id_teachers = :id:";
				$values = array('id' => $id);

		$subjects = $app->modelsManager->executeQuery($phql,$values);

			$data = array();
			foreach ($subjects as $subject) {
				$data[] = array(
					'id_teachers'   => $subject->id_teachers,
					'user_id'		=> $subject->user_id,
					'full_name'		=> $subject->full_name,
					'birthday'      => $subject->birthday,
					'gender'   		=> $subject->gender,
					'address'   	=> $subject->address,
					'city' 			=> $subject->city,
					'provinces' 	=> $subject->provinces,
					'districts' 	=> $subject->districts,
					'villages' 		=> $subject->villages

				);
			}

			$redata = array(
				'area_id' => 'subjects',
				'items'   => $data,
			);

			echo json_encode($redata);

	});

$app->get('/getstudent/{id:[0-9]}', function ($id) use ($app) {

		$phql = "SELECT * FROM PrfStudents WHERE user_id = :id:";
		$values = array('id' => $id);
		$subject = $app->modelsManager->executeQuery($phql, $values)->getFirst();

		$response = new Response();

		if ($subject == FALSE) {
			$response->setJsonContent(
				array(
					'status' => 'NOT-FOUND',
				)
			);
		} else {
			$response->setJsonContent(
				array(
					'area_id' => 'subjects',
					'id_students'   => $subject->id_students,
					'user_id'		=> $subject->user_id,
					'full_name'		=> $subject->full_name,
					'birthday'      => $subject->birthday,
					'gender'   		=> $subject->gender,
					'descriptions'  => $subject->descriptions,
					'address'   	=> $subject->address,
					'city' 			=> $subject->city,
					'provinces' 	=> $subject->provinces,
					'districts' 	=> $subject->districts,
					'villages' 		=> $subject->villages
				)
			);
		}

		return $response;
	});


$app->post("/teachersedit/{id:[0-9]+}", function ($id) use ($app) {

		$phql = "UPDATE PrfTeachers SET user_id = :user_id:, full_name = :full_name:, birthday = :birthday:, gender = :gender:, address = :address:, city = :city:, provinces = :provinces:, districts = :districts:, villages = :villages: WHERE id_teachers = :id_teachers:";

		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'id_teachers'   => $id,
				'user_id'		=> $_POST['user_id'],
				'full_name'		=> $_POST['full_name'],
				'birthday'      => $_POST['birthday'],
				'gender'   		=> $_POST['gender'],
				'address'   	=> $_POST['address'],
				'city' 			=> $_POST['city'],
				'provinces' 	=> $_POST['provinces'],
				'districts' 	=> $_POST['districts'],
				'villages' 		=> $_POST['villages'],
			]
		);

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Updated"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
	}
);

$app->post("/studentsedit/{id:[0-9]+}", function ($id) use ($app) {

		$phql = "UPDATE PrfStudents SET full_name = :full_name:, birthday = :birthday:, gender = :gender:, descriptions = :descriptions:, address = :address:, city = :city:, provinces = :provinces:, districts = :districts:, villages = :villages: WHERE user_id = :user_id:";

		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'user_id'   => $id,
				'full_name'		=> $_POST['full_name'],
				'birthday'      => $_POST['birthday'],
				'gender'   		=> $_POST['gender'],
				'descriptions'  => $_POST['descriptions'],
				'address'   	=> $_POST['address'],
				'city' 			=> $_POST['city'],
				'provinces' 	=> $_POST['provinces'],
				'districts' 	=> $_POST['districts'],
				'villages' 		=> $_POST['villages'],
			]
		);

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Updated"
				]
			);
		} else {
			$response->setStatusCode(409, "Conflict");

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					"status"   => "ERROR",
					"messages" => $errors,
				]
			);
		}

		return $response;
	}
);

$app->post("/editPassword/{uid}", function ($uid) use ($app) {

		$phql = "UPDATE Users SET password = :password: WHERE uid = :uid:";

		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'uid'   		=> $uid,
				'password'	=> md5($_POST['password']),
			]
		);

		$response = new Response();

		if ($status->success() === true) {
			$response->setJsonContent(
				[
					"status" => "Updated"
				]
			);
		} else {

			$response->setJsonContent(
				[
					"status"   => "ERROR",
				]
			);
		}

		return $response;
	}
);

$app->post("/editPasswordStudent/{uid}", function ($uid) use ($app) {

		$phql = "UPDATE Users SET password=:password: WHERE uid=:uid: AND account_type='1'";

		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'uid'   		=> $uid,
				'password'	=> md5($_POST['password']),
			]
		);

		$response = new Response();

	    if ($status->success() === true) {
            $response->setJsonContent(
                [
                    'status' => 'Updated'
                ]
            );
        } else {

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors,
                ]
            );
        }
	return $response;
}
);

$app->post("/editPasswordTeacher/{uid}", function ($uid) use ($app) {

		$phql = "UPDATE Users SET password = :password: WHERE uid = :uid: AND account_type = '0'";

		$status = $app->modelsManager->executeQuery(
			$phql,
			[
				'uid'   		=> $uid,
				'password'	=> md5($_POST['password']),
			]
		);

		$response = new Response();

		if (count($status) == 0) {
				$response->setJsonContent(
					array(
						'status' => 'Updated'
					)
				);
	   } else {

			$response->setJsonContent(
				array(
						'status' => 'ERROR'
					)
			);
		}
	return $response;
}
);


// Deletes users based on primary key ($id_user)
$app->delete('/users/{id:[0-9]+}', function ($id_user) use ($app) {

		$phql = "DELETE FROM Users WHERE id_user = :id_user: ";

		$values = array(
			'id_user' => $id_user,
		);

		$results = $app->modelsManager->executeQuery($phql, $values);

		$response = new Response();

		if ($results->success() == TRUE) {

			$response->setStatusCode(200, "Deleted");

			$response->setJsonContent(
				array(
					'status' => 'Deleted',
				)
			);
		} else {

			$response->setStatusCode(409, "Conflict");

			$errors = array();
			foreach ($results->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				array(
					'status'   => 'ERROR',
					'messages' => $errors,
				)
			);
		}

		return $response;
	});

$app->get('/users/{id:[0-9]+}', function ($id_user) use ($app) {

		$phql = "SELECT * FROM Users WHERE id_user = :id_user: ";
		$values = array('id_user' => $id_user);
		$user = $app->modelsManager->executeQuery($phql, $values)->getFirst();

		$response = new Response();

		if ($user == FALSE) {
			$response->setJsonContent(
				array(
					'status' => 'NOT-FOUND',
				)
			);
		} else {
			$response->setJsonContent(
				array(
					'status'      => 'FOUND',
					'data'        => array(
						'id_user'    => $user->id_user,
						'email'      => $user->email,
						'username'   => $user->username,
						'password'   => $user->password,
						'created_by' => $user->created_by,
						'created_at' => $user->created_at,
						'updated_by' => $user->updated_by,
						'updated_at' => $user->updated_at
					)
				)
			);
		}

		return $response;
	});

$app->get('/packagedays/{days:[0-9]+}/{times}/{city}/{district}/{halaman:[0-9]+}/{limit:[0-9]+}/{orderby}', function($days, $times, $city, $district, $page, $limit, $orderby) use ($app) {
	
		$hal  	= $page ==1 ? $page*0 : $page-1;
		$pages 	= $hal*$limit;

		$phql 	= "SELECT d.email,a.schedule_days, a.schedule_times, b.id_teachers, b.user_id, b.full_name, b.birthday, b.gender, b.current_job, b.descriptions, b.address, b.city, b.provinces, b.districts, b.villages, c.city, c.district FROM Schedules as a, PrfTeachers as b, RadiusLocations as c, Users as d WHERE a.schedule_days = :days: AND a.schedule_times LIKE :tims: AND a.user_id = b.user_id AND c.user_id = a.user_id AND b.user_id = d.id_user AND c.city LIKE :city: AND c.district LIKE :district: ORDER BY $orderby LIMIT $pages, $limit";
		
		$schedules = $app->modelsManager->executeQuery($phql,
			[
				'days' => selectDay($days),
				'tims' => '%'.$times.'%',
				'city' => '%'.$city.'%',
				'district' => '%'.$district.'%'
			]
		);	

		$data = array();
		foreach ($schedules as $schedule) {

		$phql2	= "SELECT rating FROM Rating WHERE teacher_id=$schedule->id_teachers";

		$ratings = $app->modelsManager->executeQuery($phql2);	

		$dataRate = 0;
		$avgRating = 0;
		$dataCount=array();
		foreach ($ratings as $rating) {
			$dataRate +=$rating->rating;
			$dataCount[]=$rating->rating;
		}
		if(count($dataCount)>0){
		$avgRating=$dataRate/count($dataCount);
		}
			$data[] = array
			(
				'user_id'    	=> $schedule->user_id,
				'id_teacher'    => $schedule->id_teachers,
				'full_name'    	=> $schedule->full_name,
				'email'    		=> $schedule->email,
				'birthday'    	=> $schedule->birthday,
				'gender'    	=> $schedule->gender,
				'current_job'   => $schedule->current_job,
				'descriptions'  => $schedule->descriptions,
				'address'    	=> $schedule->address,
				'city'    		=> $schedule->city,
				'provinces'    	=> $schedule->provinces,
				'districs'    	=> $schedule->districts,
				'villages'    	=> $schedule->villages,
				'schedule_days' => $schedule->schedule_days,
				// 'rating_count'    => count($dataCount),
				'rating'    => $avgRating,
				'schedule_times'=> $schedule->schedule_times
			);
		}

		$redata = array(
			'area_id' 	=> 'packagedays',
			'page' 		=> intval($page),
			'count' 	=> count($data),
			'items'   	=> $data,
		);
		echo json_encode($redata);
		// echo "<pre>".print_r($redata, 1)."</pre>";die();
	});

$app->get('/packageweeks/{days}/{times}/{city}/{district}/{halaman:[0-9]+}/{limit:[0-9]+}/{orderby}', function($days, $times, $city, $district, $page, $limit, $orderby) use ($app) {
	
		$hal  	= $page==1 ? $page*0 : $page-1;
		$pages 	= $hal*$limit;

		$exp=explode(",",$days);
		$explo= explode(",",implode(',', array_map('selectDay', $exp)));
		$dataArr = "'" . implode("','", $explo) . "'";

		// $phql 	= "SELECT a.id_teachers, a.user_id, a.full_name, a.birthday, a.gender, a.address, a.city, a.provinces, a.districts, a.villages, b.schedule_id, b.user_id, b.schedule_days, b.schedule_times, c.city, c.district FROM PrfTeachers as a, Schedules as b, RadiusLocations as c WHERE b.schedule_days IN($dataArr) AND b.schedule_times LIKE :tims: AND a.user_id = b.user_id AND c.user_id = a.user_id AND c.user_id = b.user_id AND c.city LIKE :city: AND c.district LIKE :district: GROUP BY b.schedule_days HAVING COUNT(b.schedule_days)>=2 ORDER BY :orderby:";

		$phql ="SELECT a.id_teachers, a.user_id, a.full_name, a.birthday, a.gender, a.current_job, a.descriptions, a.address, a.city, a.provinces, a.districts, a.villages, b.schedule_id, b.user_id, b.schedule_days, b.schedule_times, c.city, c.district FROM PrfTeachers as a, Schedules as b, RadiusLocations as c WHERE a.user_id = b.user_id AND b.schedule_times LIKE :tims: AND b.schedule_days IN($dataArr) AND c.user_id = a.user_id AND c.user_id = b.user_id AND c.city LIKE :city: AND c.district LIKE :district: GROUP BY a.user_id HAVING COUNT(a.id_teachers)>=:countInput: ORDER BY :orderby:";

		$schedules = $app->modelsManager->executeQuery($phql,
			[
				'orderby' => $orderby,
				'tims' => '%'.$times.'%',
				'city' => '%'.$city.'%',
				'district' => '%'.$district.'%',
				'countInput' => count($exp)
			]
		);	

		$data = array();
		foreach ($schedules as $schedule) {
		
		$phql2	= "SELECT rating FROM Rating WHERE teacher_id=$schedule->id_teachers";

		$ratings = $app->modelsManager->executeQuery($phql2);	

		$dataRate = 0;
		$avgRating = 0;
		$dataCount=array();
		foreach ($ratings as $rating) {
			$dataRate +=$rating->rating;
			$dataCount[]=$rating->rating;
		}
		if(count($dataCount)>0){
		$avgRating=$dataRate/count($dataCount);
		}

			$data[] = array(
				'id_teachers'	=> $schedule->id_teachers,
				'user_id'    	=> $schedule->user_id,
				'full_name'    	=> $schedule->full_name,
				'birthday'    	=> $schedule->birthday,
				'gender'    	=> $schedule->gender,
				'current_job'   => $schedule->current_job,
				'descriptions'  => $schedule->descriptions,
				'address'    	=> $schedule->address,
				'city'    		=> $schedule->city,
				'provinces'    	=> $schedule->provinces,
				'districs'    	=> $schedule->districts,
				'villages'    	=> $schedule->villages,

				'schedule_id'	=> $schedule->schedule_id,
				'user_id'    	=> $schedule->user_id,
				'schedule_days' => $schedule->schedule_days,
				'rating'    	=> $avgRating,
				'schedule_times'=> $schedule->schedule_times
			);
		}

		$redata = array( 
			'area_id' 	=> 'packageweeks',
			'packages2'	=> intval($page),
			'count' 	=> count($data),
			'items'   	=> $data,
		);

		echo json_encode($redata);
		// echo "<pre>".print_r($redata, 1)."</pre>";die();
	});


function myfunction($num)
		{
		  if($num==1){
		    return "kamis";
		    }else{
		    return $num;
		    }
		}

		function selectDays($num)
		{
		  if($num==20170420){
		    return "kamis";
		    }else{
		    return $num;
		    }
		}


function selectDay($input){
	switch(date("D", strtotime($input))){
		  case "Sun":
		   $day = "Ahad";
		  break;
		  case "Mon":
		   $day = "Senin";
		  break;
		  case "Tue":
		   $day = "Selasa";
		  break;
		  case "Wed":
		   $day = "Rabu";
		  break;
		  case "Thu":
		   $day = "Kamis";
		  break;
		  case "Fri":
		   $day = "Jumat";
		  break;
		  case "Sat":
		   $day = "Sabtu";
		  break;
		}
		return $day;
}

$app->notFound(function () use ($app) {
		$app->response->setStatusCode(404, "Not Found")->sendHeaders();
		echo 'Halaman index Belum ada isi :D .';
	});

$app->handle();
?>




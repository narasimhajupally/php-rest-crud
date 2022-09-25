<?php
class AssessmentController extends BaseController
{
    public function list($m_id)
    {
        $strErrorDesc = '';

        if (!is_numeric($m_id)) {
            $strErrorDesc = 'module id in path should be numeric';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }

        try {
            $model = new ModuleModel();
            if (!$model->moduleExists($m_id)) {
                $strErrorDesc = 'module not found';
                $strErrorHeader = 'HTTP/1.0 404 Not Found';
                goto output;
            }
            $model = new AssessmentModel();
            $assessments = $model->getAssessments($m_id);
            $responseData = json_encode($assessments);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }

        output:
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('status' => 'fail', 'message' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function create($m_id)
    {
        $input = json_decode(file_get_contents("php://input"), true);
        // print_r($input);exit;
        $strErrorDesc = '';

        if (!is_numeric($m_id)) {
            $strErrorDesc = 'module id variable in url path should be numeric';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }

        if (!isset($input['type']) || !isset($input['description']) || !isset($input['weight'])) {
            $strErrorDesc = 'mandatory fields are missing, please check sample';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }

        $type = $input['type'];
        $description = $input['description'];
        $weight = $input['weight'];

        if (!in_array($type, ["written exam", "classtest", "assignment", "performance"])) {
            $strErrorDesc = 'unknown assessment type. allowed types : written exam, classtest, assignment, performance';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }
        if (strlen($description) > 500) {
            $strErrorDesc = 'Description length should not exceed 500 characters';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }
        if (!is_numeric($weight) || intval($weight < 0) || intval($weight > 100)) {
            $strErrorDesc = 'weight of an assessment should be between 0 and 100.';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }
        try {
            $model = new ModuleModel();
            if (!$model->moduleExists($m_id)) {
                $strErrorDesc = 'module not found';
                $strErrorHeader = 'HTTP/1.0 404 Not Found';
                goto output;
            }
            $weight=intval($weight);
            $model = new AssessmentModel();
            $totalWeight = $model->getTotalWeight($m_id);
            if ($totalWeight + $weight > 100) {
                $strErrorDesc = 'total weight of the assessments of a module cannot exceed 100.';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                goto output;
            }
            $model->addAssessment($m_id, $type, $description, $weight);
            $responseData = ['status' => 'success', 'message' => 'Assessment added Successfully'];
            $responseData = json_encode($responseData);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }

        output:
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('status' => 'fail', 'message' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function update($m_id, $a_id)
    {
        $input = json_decode(file_get_contents("php://input"), true);
        // print_r($input);exit;
        $strErrorDesc = '';

        if (!is_numeric($m_id) || !is_numeric($a_id)) {
            $strErrorDesc = 'module id and assessment id variables in url path should be numeric';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }

        if (!isset($input['type']) || !isset($input['description']) || !isset($input['weight'])) {
            $strErrorDesc = 'mandatory fields are missing, please check sample';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }

        $type = $input['type'];
        $description = $input['description'];
        $weight = $input['weight'];

        if (!in_array($type, ["written exam", "classtest", "assignment", "performance"])) {
            $strErrorDesc = 'unknown assessment type. allowed types : written exam, classtest, assignment, performance';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }
        if (strlen($description) > 500) {
            $strErrorDesc = 'Description length should not exceed 500 characters';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }
        if (!is_numeric($weight) || intval($weight < 0) || intval($weight > 100)) {
            $strErrorDesc = 'weight of an assessment should be between 0 and 100.';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }
        try {
            $model = new ModuleModel();
            if (!$model->moduleExists($m_id)) {
                $strErrorDesc = 'module not found';
                $strErrorHeader = 'HTTP/1.0 404 Not Found';
                goto output;
            }
            $weight=intval($weight);
            $model = new AssessmentModel();
            $old = $model->getAssessment($a_id, $m_id);
            if (empty($old)) {
                $strErrorDesc = 'assessment not found';
                $strErrorHeader = 'HTTP/1.0 404 Not Found';
                goto output;
            }
            $totalWeight = $model->getTotalWeight($m_id);
            if ($totalWeight - $old['weight'] + $weight > 100) {
                $strErrorDesc = 'total weight of the assessments of a module cannot exceed 100.';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                goto output;
            }
            $model->updateAssessment($a_id, $type, $description, $weight);
            $responseData = ['status' => 'success', 'message' => 'Assessment updated Successfully'];
            $responseData = json_encode($responseData);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }

        output:
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('status' => 'fail', 'message' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function delete($a_id)
    {
        $strErrorDesc = '';
        if (!is_numeric($a_id)) {
            $strErrorDesc = 'assessment id variable in url path should be numeric';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }

        try {
            $model = new AssessmentModel();
            $assessment = $model->getAssessment($a_id);
            if (empty($assessment)) {
                $strErrorDesc = 'assessment not found';
                $strErrorHeader = 'HTTP/1.0 404 Not Found';
                goto output;
            }
            $model->delete($a_id);
            $responseData = ['status' => 'success', 'message' => 'Assessment deleted Successfully'];
            $responseData = json_encode($responseData);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }

        output:
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('status' => 'fail', 'message' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}

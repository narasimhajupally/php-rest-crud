<?php
class ModuleController extends BaseController
{
    public function list()
    {
        $strErrorDesc = '';

        try {
            $model = new ModuleModel();
            $modules = $model->getModules();
            $responseData = json_encode($modules);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }

        // send output
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

    public function create()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        $strErrorDesc = '';

        if (!isset($input['code']) || !isset($input['title'])) {
            $strErrorDesc = 'mandatory fields are missing, please check sample';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }
        $code = $input['code'];
        $title = $input['title'];

        if (strlen($title)>100) {
            $strErrorDesc = 'Title length should not exceed 100 characters';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }

        if (preg_match("/^\d{6}-[A-Z0-9]{1,8}$/", $code) != 1) {
            $strErrorDesc = 'A module code should contain a sequence of six digits (0-9), followed by a hyphen, followed
            by a sequence of upper case letters (A-Z) and digits (0-9) with a total length of at most 15 characters';
            $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            goto output;
        }

        try {
            $model = new ModuleModel();
            $old = $model->getModuleByCode($code);
            if (!empty($old)) {
                $strErrorDesc = "Module with code:$code already exists.";
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                goto output;
            }
            $model->addModule($code, $title);
            $responseData = ['status' => 'success', 'message' => 'Module added Successfully'];
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

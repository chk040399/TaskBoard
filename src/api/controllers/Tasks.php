<?php
use RedBeanPHP\R;

class Tasks extends BaseController {

    public function getTask($request, $response, $args) {
        $status = $this->secureRoute($request, $response,
            SecurityLevel::User);
        if ($status !== 200) {
            return $this->jsonResponse($response, $status);
        }

        $task = new Task($this->container, (int)$args['id']);

        if ($task->id === 0) {
            $this->logger->addError('Attemt to load task ' .
                $args['id'] . ' failed.');
            $this->apiJson->addAlert('error', 'No task found for ID ' .
                $args['id'] . '.');

            return $this->jsonResponse($response);
        }

        if (!$this->checkBoardAccess($this->getBoardId($task->id), $request)) {
            return $this->jsonResponse($response, 403);
        }

        $this->apiJson->setSuccess();
        $this->apiJson->addData($task);

        return $this->jsonResponse($response);
    }

    public function addTask($request, $response, $args) {
        $status = $this->secureRoute($request, $response,
            SecurityLevel::User);
        if ($status !== 200) {
            return $this->jsonResponse($response, $status);
        }

        $task = new Task($this->container);
        $task->loadFromJson($request->getBody());

        $column = new Column($this->container, $task->column_id);

        if ($column->id === 0) {
            $this->logger->addError('Add Task: ', [$task]);
            $this->apiJson->addAlert('error', 'Error adding task. ' .
                'Please check your entries and try again.');

            return $this->jsonResponse($response);
        }

        if (!$this->checkBoardAccess($column->board_id, $request)) {
            return $this->jsonResponse($response, 403);
        }

        $task->save();

        $actor = new User($this->container, Auth::GetUserId($request));

        $this->dbLogger->logChange($this->container, $actor->id,
            $actor->username . ' added task ' . $task->title . '.',
            '', json_encode($task), 'task', $task->id);

        $this->apiJson->setSuccess();
        $this->apiJson->addAlert('success', 'Task ' .
            $task->title . ' added.');

        return $this->jsonResponse($response);
    }

    public function updateTask($request, $response, $args) {
        $status = $this->secureRoute($request, $response,
            SecurityLevel::User);
        if ($status !== 200) {
            return $this->jsonResponse($response, $status);
        }

        $actor = new User($this->container, Auth::GetUserId($request));

        $id = (int)$args['id'];
        $task = new Task($this->container, $id);

        $update = new Task($this->container);
        $update->loadFromJson($request->getBody());

        if ($task->id !== $update->id) {
            $this->logger->addError('Update Task: ', [$task, $update]);
            $this->apiJson->addAlert('error', 'Error updating task ' .
                $task->title . '. Please try again.');

            return $this->jsonResponse($response);
        }

        if (!$this->checkBoardAccess($this->getBoardId($task->column_id),
                $request)) {
            return $this->jsonResponse($response, 403);
        }

        $update->save();

        $this->dbLogger->logChange($this->container, $actor->id,
            $actor->username . ' updated task ' . $task->title,
            json_encode($task), json_encode($update),
            'task', $update->id);

        $this->apiJson->setSuccess();
        $this->apiJson->addAlert('success', 'Task ' .
            $update->title . ' updated.');

        return $this->jsonResponse($response);
    }

    public function removeTask($request, $response, $args) {
        $status = $this->secureRoute($request, $response,
            SecurityLevel::User);
        if ($status !== 200) {
            return $this->jsonResponse($response, $status);
        }

        $actor = new User($this->container, Auth::GetUserId($request));

        $id = (int)$args['id'];
        $task = new Task($this->container, $id);

        if ($task->id !== $id) {
            $this->logger->addError('Remove Task: ', [$task]);
            $this->apiJson->addAlert('error', 'Error removing task. ' .
                'No task found for ID ' . $id . '.');

            return $this->jsonResponse($response);
        }

        if (!$this->checkBoardAccess($this->getBoardId($task->id), $request)) {
            return $this->jsonResponse($response, 403);
        }

        $before = $task;
        $task->delete();

        $this->dbLogger->logChange($this->container, $actor->id,
            $actor->username . ' removed task ' . $before->title,
            json_encode($before), '', 'task', $id);

        $this->apiJson->setSuccess();
        $this->apiJson->addAlert('success',
            'Task ' . $before->title . ' removed.');

        return $this->jsonResponse($response);
    }

    private function getBoardId($taskId) {
        $task = new Task($this->container, $taskId);

        $column = new Column($this->container, $task->column_id);

        return $column->board_id;
    }
}


<?php
use RedBeanPHP\R;

class Columns extends BaseController {

    public function getColumn($request, $response, $args) {
        $status = $this->secureRoute($request, $response,
            SecurityLevel::User);
        if ($status !== 200) {
            return $this->jsonResponse($response, $status);
        }

        $column = new Column($this->container, (int)$args['id']);

        if ($column->id === 0) {
            $this->logger->addError('Attempt to load column ' .
                $args['id'] . ' failed.');
            $this->apiJson->addAlert('error', 'No column found for ID ' .
                $args['id'] . '.');

            return $this->jsonResponse($response);
        }

        if (!$this->checkBoardAccess($column->board_id, $request)) {
            return $this->jsonResponse($response, 403);
        }

        $this->apiJson->setSuccess();
        $this->apiJson->addData($column);

        return $this->jsonResponse($response);
    }

    public function addColumn($request, $response, $args) {
        $status = $this->secureRoute($request, $response,
            SecurityLevel::BoardAdmin);
        if ($status !== 200) {
            return $this->jsonResponse($response, $status);
        }

        $column = new Column($this->container);
        $column->loadFromJson($request->getBody());

        $board = new Board($this->container, $column->board_id);

        if ($board->id === 0) {
            $this->logger->addError('Add Column: ', [$column]);
            $this->apiJson->addAlert('error', 'Error adding column. ' .
                'Please try again.');

            return $this->jsonResponse($response);
        }

        if (!$this->checkBoardAccess($column->board_id, $request)) {
            return $this->jsonResponse($response, 403);
        }

        $column->save();

        $actor = new User($this->container, Auth::GetUserId($request));

        $this->dbLogger->logChange($this->container, $actor->id,
            $actor->username . ' added column ' . $column->name . '.',
            '', json_encode($column), 'column', $column->id);

        $this->apiJson->setSuccess();
        $this->apiJson->addAlert('success', 'Column ' .
            $column->name . ' added.');

        return $this->jsonResponse($response);
    }

    public function updateColumn($request, $response, $args) {
        $status = $this->secureRoute($request, $response,
            SecurityLevel::BoardAdmin);
        if ($status !== 200) {
            return $this->jsonResponse($response, $status);
        }

        $column = new Column($this->container, (int)$args['id']);

        if (!$this->checkBoardAccess($column->board_id, $request)) {
            return $this->jsonResponse($response, 403);
        }

        $update = new Column($this->container);
        $update->loadFromJson($request->getBody());

        if ($column->id !== $update->id) {
            $this->logger->addError('Update Column: ', [$column, $update]);
            $this->apiJson->addAlert('error', 'Error updating column ' .
                $update->name . '. Please try again.');

            return $this->jsonResponse($response);
        }

        $update->save();

        $actor = new User($this->container, Auth::GetUserId($request));
        $this->dbLogger->logChange($this->container, $actor->id,
            $actor->username . ' updated column ' . $update->name,
            json_encode($column), json_encode($update),
            'column', $update->id);

        $this->apiJson->setSuccess();
        $this->apiJson->addAlert('success', 'Column ' .
            $update->name . ' updated.');

        return $this->jsonResponse($response);
    }

    public function removeColumn($request, $response, $args) {
        $status = $this->secureRoute($request, $response,
            SecurityLevel::BoardAdmin);
        if ($status !== 200) {
            return $this->jsonResponse($response, $status);
        }

        $id = (int)$args['id'];
        $column = new Column($this->container, $id);

        if ($column->id !== $id) {
            $this->logger->addError('Remove Column: ', [$column]);
            $this->apiJson->addAlert('error', 'Error removing column. ' .
                'No column found for ID ' . $id . '.');

            return $this->jsonResponse($response);
        }

        if (!$this->checkBoardAccess($column->board_id, $request)) {
            return $this->jsonResponse($response, 403);
        }

        $before = $column;
        $column->delete();

        $actor = new User($this->container, Auth::GetUserId($request));
        $this->dbLogger->logChange($this->container, $actor->id,
            $actor->username . ' removed column ' . $before->name,
            json_encode($before), '', 'column', $id);

        $this->apiJson->setSuccess();
        $this->apiJson->addAlert('success',
            'Column ' . $before->name . ' removed.');

        return $this->jsonResponse($response);
    }
}


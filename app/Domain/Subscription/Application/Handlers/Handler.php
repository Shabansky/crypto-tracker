<?php

namespace App\Domain\Subscription\Application\Handlers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use PDOException;
use stdClass;

abstract class Handler
{
    protected Request $request;
    protected stdClass $requestContent;

    public function run(Request $request): Response
    {
        $this->request = $request;
        $this->requestContent = new stdClass;

        $content = trim($this->request->getContent());

        try {
            $this->validateBodyAsJson($content);
            $this->validate();
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }

        try {
            return $this->process();
        } catch (InvalidArgumentException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 409);
        } catch (QueryException | PDOException $e) {
            //TODO: Log this
            var_dump($e->getMessage());
            return new Response('General error', 500);
        }
    }

    protected function validateBodyAsJson(string $body)
    {
        if (!empty($body)) {
            if (!json_validate($body)) {
                throw new InvalidArgumentException("Request body is not a JSON");
            }

            $this->requestContent = json_decode($body);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    protected abstract function validate();

    //TODO: Consider process returning void and introduce specific method for response return.
    /**
     * @throws QueryException
     * @throws PDOException
     * @throws InvalidArgumentException
     */
    protected abstract function process(): Response;
}

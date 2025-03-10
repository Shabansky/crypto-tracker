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

        $content = trim($this->request->getContent());

        try {
            if (!json_validate($content)) {
                throw new InvalidArgumentException("Request body is not a JSON");
            }

            $this->requestContent = json_decode($content);

            $this->validate();
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }

        try {
            return $this->process();
        } catch (QueryException | PDOException $e) {
            //TODO: Log this
            return new Response('General error', 500);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    protected abstract function validate();

    /**
     * @throws QueryException
     * @throws PDOException
     */
    protected abstract function process(): Response;
}

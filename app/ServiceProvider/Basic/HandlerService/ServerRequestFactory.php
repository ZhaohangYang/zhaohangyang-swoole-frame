<?php
declare(strict_types=1);

namespace App\ServiceProvider\Basic\HandlerService;

use function Laminas\Diactoros\marshalHeadersFromSapi;
use function Laminas\Diactoros\marshalMethodFromSapi;
use function Laminas\Diactoros\marshalProtocolVersionFromSapi;
use function Laminas\Diactoros\marshalUriFromSapi;
use function Laminas\Diactoros\normalizeServer;
use function Laminas\Diactoros\normalizeUploadedFiles;
use function Laminas\Diactoros\parseCookieHeader;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class for marshaling a request object from the current PHP environment.
 *
 * Logic largely refactored from the Laminas Laminas\Http\PhpEnvironment\Request class.
 *
 * @copyright Copyright (c) 2005-2015 Laminas (https://www.zend.com)
 * @license   https://getlaminas.org/license/new-bsd New BSD License
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Function to use to get apache request headers; present only to simplify mocking.
     *
     * @var callable
     */
    private $apacheRequestHeaders = 'apache_request_headers';

    /**
     * Create a request from the supplied superglobal values.
     *
     * If any argument is not supplied, the corresponding superglobal value will
     * be used.
     *
     * The ServerRequest created is then passed to the fromServer() method in
     * order to marshal the request URI and headers.
     *
     * @param array|null $server $_SERVER superglobal
     * @param array|null $query $_GET superglobal
     * @param array|null $body $_POST superglobal
     * @param array|null $cookies $_COOKIE superglobal
     * @param array|null $files $_FILES superglobal
     * @return ServerRequest
     * @see fromServer()
     */
    public function fromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null,
        $content = null
    ) : ServerRequest
    {

        $server = normalizeServer(
            $server ?: $_SERVER,
            is_callable( $this->apacheRequestHeaders ) ? $this->apacheRequestHeaders : null
        );

        $files   = normalizeUploadedFiles( $files ?: $_FILES );
        $headers = marshalHeadersFromSapi( $server );

        if ( null === $cookies && array_key_exists( 'cookie', $headers ) ) {
            $cookies = parseCookieHeader( $headers['cookie'] );
        }

        $factory = new StreamFactory();

        return new ServerRequest(
            $server,
            $files,
            marshalUriFromSapi( $server, $headers ),
            marshalMethodFromSapi( $server ),
            $factory->createStream( $content ),
            $headers,
            $cookies ?: $_COOKIE,
            $query ?: $_GET,
            $body ?: $_POST,
            marshalProtocolVersionFromSapi( $server )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []) : ServerRequestInterface
    {
        $uploadedFiles = [];

        return new ServerRequest(
            $serverParams,
            $uploadedFiles,
            $uri,
            $method,
            'php://temp'
        );
    }
}
<?php

/**
 * Session repository.
 *
 * Reads and writes sessions.
 **/

declare(strict_types=1);

namespace App\Services;

use App\Services\DatabaseService;
use Psr\Http\Message\RequestInterface;

class SessionService
{
    public const COOKIE = 'session_id';

    /**
     * @var DatabaseService
     **/
    protected $db;

    public function __construct(DatabaseService $db)
    {
        $this->db = $db;
    }

    public function getData(RequestInterface $request): array
    {
        $sid = $this->getSessionId($request);

        if (null === $sid) {
            return [];
        }

        $entry = $this->db->fetchOne('SELECT * FROM sessions WHERE id = ?', [$sid]);

        if (empty($entry)) {
            return [];  // expired
        }

        $data = unserialize($entry['data']);
        return $data;
    }

    public function setData(RequestInterface $request, array $data): void
    {
        $sid = $this->getSessionId($request);

        if (null === $sid) {
            $sid = bin2hex(random_bytes(16));
            setcookie(self::COOKIE, $sid, time() + 86400 * 30, '/');  // FIXME: middleware?
        }

        $now = strftime('%Y-%m-%d %H:%M:%S');

        $this->db->query('REPLACE INTO `sessions` (`id`, `updated`, `data`) '
            . 'VALUES (?, ?, ?)', [$sid, $now, serialize($data)]);
    }

    protected function getSessionId(RequestInterface $request): ?string
    {
        $cookies = $request->getCookieParams();
        if (!empty($cookies[self::COOKIE])) {
            return $cookies[self::COOKIE];
        }

        return $cookies[self::COOKIE];
    }
}

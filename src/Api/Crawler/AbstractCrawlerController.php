<?php

declare(strict_types=1);

namespace App\Api\Crawler;

use App\Api\Controller\AbstractController;
use Apitte\Core\Annotation\Controller as Api;

/**
 * @Api\Path("/crawler")
 */
abstract class AbstractCrawlerController extends AbstractController
{
}

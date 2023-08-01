<?php

declare(strict_types=1);

namespace App\Api\Crawler;

use Apitte\Core\Annotation\Controller as Api;
use App\Api\Controller\AbstractController;

/**
 * @Api\Path("/crawler")
 */
abstract class AbstractCrawlerController extends AbstractController
{
}

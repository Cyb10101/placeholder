<?php
namespace App\Traits;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Trait ControllerTrait
 */
trait ControllerTrait {
    /**
     * @return string
     */
    protected function getProjectDirectory(): string {
        return $this->getParameter('kernel.project_dir');
    }

    /**
     * @return string
     */
    protected function getCacheDirectory(): string {
        $projectDirectory = $this->getParameter('kernel.cache_dir');
        $path = $projectDirectory . '/images';
        $filesystem = new Filesystem();
        $filesystem->mkdir($path);
        return $path;
    }

    /**
     * @return bool
     */
    protected function isProduction(): bool {
        return ($this->getParameter('kernel.environment') === 'prod');
    }

    /**
     * @return bool
     */
    protected function isTesting(): bool {
        return ($this->getParameter('kernel.environment') === 'test');
    }

    /**
     * @return bool
     */
    protected function isDevelopment(): bool {
        return ($this->getParameter('kernel.environment') === 'dev');
    }
}

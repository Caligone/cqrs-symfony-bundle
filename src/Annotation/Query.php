<?php

namespace CQRS\Annotation;

/**
 * Annotation class for @Query()
 * 
 * @Annotation
 */
class Query extends AbstractAnnotation
{
    protected string $viewModel;
    protected array $methods = ['GET'];

    public function __construct(array $data)
    {
        parent::__construct($data);
        if (!isset($data['viewModel'])) {
            throw new \BadMethodCallException(sprintf('Missing property "viewModel" on annotation "%s".', static::class));
        }
        $this->setViewModel($data['viewModel']);
    }

    public function getViewModel(): string
    {
        return $this->viewModel;
    }

    public function setViewModel(string $viewModel): void
    {
        $this->viewModel = $viewModel;
    }
}

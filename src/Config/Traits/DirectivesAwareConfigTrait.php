<?php
/**
 * Date: 03/17/2017
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Config\Traits;


use Youshido\GraphQL\Directive\Directive;
use Youshido\GraphQL\Field\InputField;

trait DirectivesAwareConfigTrait
{
    protected array $directives = [];
    protected bool $_isDirectivesBuilt;

    public function buildDirectives(): void
    {
        if ($this->_isDirectivesBuilt) {
            return;
        }

        if (!empty($this->data['directives'])) {
            $this->addDirectives($this->data['directives']);
        }
        $this->_isDirectivesBuilt = true;
    }

    public function addDirectives(array $directiveList): static
    {
        foreach ($directiveList as $directiveName => $directiveInfo) {
            if ($directiveInfo instanceof Directive) {
                $this->directives[$directiveInfo->getName()] = $directiveInfo;
                continue;
            } else {
                $this->addDirective($directiveName, $this->buildConfig($directiveName, $directiveInfo));
            }
        }

        return $this;
    }

    public function addDirective(mixed $directive, mixed $directiveInfo = null): static
    {
        if (!($directive instanceof Directive)) {
            $directive = new Directive($this->buildConfig($directive, $directiveInfo));
        }
        $this->directives[$directive->getName()] = $directive;

        return $this;
    }

    public function getDirective(string $name): ?InputField
    {
        return $this->hasDirective($name) ? $this->directives[$name] : null;
    }

    public function hasDirective(string $name): bool
    {
        return array_key_exists($name, $this->directives);
    }

    public function hasDirectives(): bool
    {
        return !empty($this->directives);
    }

    public function getDirectives(): array
    {
        return $this->directives;
    }

    public function removeDirective(string $name): static
    {
        if ($this->hasDirective($name)) {
            unset($this->directives[$name]);
        }

        return $this;
    }

}

<?php declare(strict_types = 1);

namespace PHPStan\Analyser\Php;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Context;
use PHPStan\Analyser\FunctionTypeSpecifyingExtension;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\IntegerType;

class IsIntFunctionTypeSpecifyingExtension implements FunctionTypeSpecifyingExtension, TypeSpecifierAwareExtension
{

	/**
	 * @var \PHPStan\Analyser\TypeSpecifier
	 */
	private $typeSpecifier;

	public function isFunctionSupported(FunctionReflection $functionReflection, FuncCall $node, Scope $scope, Context $context): bool
	{
		return in_array(strtolower($functionReflection->getName()), [
				'is_int',
				'is_integer',
				'is_long',
			], true)
			&& isset($node->args[0])
			&& !$context->null();
	}

	public function specifyTypes(FunctionReflection $functionReflection, FuncCall $node, Scope $scope, Context $context): SpecifiedTypes
	{
		if ($context->null()) {
			throw new \PHPStan\ShouldNotHappenException();
		}

		return $this->typeSpecifier->create($node->args[0]->value, new IntegerType(), $context);
	}

	public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
	{
		$this->typeSpecifier = $typeSpecifier;
	}

}

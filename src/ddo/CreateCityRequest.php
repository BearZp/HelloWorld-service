<?php

namespace App\ddo;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class CreateCityRequest
{
    #region [properties]

    /**
     * @Assert\Length(
     *     min = 3,
     *     max = 15
     * )
     * @var string
     */
    private $name;

    /**
     * @Assert\LessThan(
     *     value = 1000
     * )
     * @var int
     */
    private $population;

    #endregion

    #region [Getters & Setters]

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPopulation(): int
    {
        return $this->population;
    }

    /**
     * @param string $name
     * @return CreateCityRequest
     */
    public function setName(string $name): CreateCityRequest
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param int $population
     * @return CreateCityRequest
     */
    public function setPopulation(int $population): CreateCityRequest
    {
        $this->population = $population;
        return $this;
    }

    #endregion

    /**
     * @return $this
     * @throws \Exception
     */
    public function validateModel(): self
    {
        $error = $this->validate();
        if ($error !== null) {
            throw new \Exception($error['message']);
        }

        return $this;
    }

    /**
     * @return array|null
     */
    private function validate(): ?array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $violations = $validator->validate($this);
        return $this->parseViolations($violations);
    }

    /**
     * parseViolations
     *
     * @param $violations
     *
     * @return array|null
     */
    private function parseViolations($violations): ?array
    {
        $errors = null;
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors['message'] = $violation->getMessage() . ' (' . $violation->getPropertyPath() . ')';
            }
        }

        return $errors;
    }


}
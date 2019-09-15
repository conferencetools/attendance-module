<?php


namespace ConferenceTools\Attendance\Test\Unit;


use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class MessageTestAbstract extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;
    private $serializer;


    protected function getSerializer(): Serializer
    {
        if ($this->serializer === null) {
            AnnotationRegistry::registerLoader('class_exists');
            $this->serializer = SerializerBuilder::create()
                ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
                ->addDefaultHandlers()
                ->build();
        }

        return $this->serializer;
    }

    protected function datetimeWithoutMs(): \DateTime
    {
        $format = 'Y-m-d h:i:s';
        $when = \DateTime::createFromFormat($format, (new \DateTime())->format($format));
        return $when;
    }
}
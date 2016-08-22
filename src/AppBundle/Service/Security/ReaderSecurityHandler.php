<?php
// src/AppBundle/Service/Security/ReaderSecurityHandler.php
namespace AppBundle\Service\Security;

use DateTime,
    DateInterval;

use Symfony\Component\Form\Form,
    Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Reader;

class ReaderSecurityHandler
{
    private $_manager;

    private $_passwordEncoder;

    public function __construct(EntityManager $manager, UserPasswordEncoder $passwordEncoder)
    {
        $this->_manager = $manager;

        $this->_passwordEncoder = $passwordEncoder;
    }

    public function registerReader(Form $form)
    {
        // cleanup expired registration requests
        $this->cleanupExpiredRegistration();

        $registration = $form->getData();

        // encode password
        $encodedPassword = $this->_passwordEncoder->encodePassword(
            $registration->getReader(),
            $registration->getReader()->getPassword()
        );

        $registration->getReader()->setPassword($encodedPassword);

        // set digest
        $registrationDigest = hash('sha256',
            $registration->getReader()->getId() . $registration->getReader()->getEmail() . $registration->getReader()->getPassword() . openssl_random_pseudo_bytes(64, $crypto_strong)
        );

        $registration->getReader()->setRegistrationDigest($registrationDigest);

        $registrationDigestDatetime = (new DateTime('now'))
            ->add(new DateInterval('PT30M'));

        $registration->getReader()->setRegistrationDigestDatetime($registrationDigestDatetime);

        $this->_manager->persist($registration->getReader());
        $this->_manager->flush();

        return $registration;
    }

    public function cleanupExpiredRegistration()
    {
        $expiredRegistrationRequests = $this->_manager->getRepository('AppBundle:Reader')->findExpiredRegistrationRequests();

        foreach($expiredRegistrationRequests as $expiredRegistrationRequest) {
            $this->_manager->remove($expiredRegistrationRequest);
        }

        $this->_manager->flush();
    }

    public function cleanupFailedRegistration($readerId)
    {
        $failedRegistrationRequest = $this->_manager->getRepository('AppBundle:Reader')->findOneBy($readerId);

        if( !$failedRegistrationRequest )
            return;

        $this->_manager->remove($failedRegistrationRequest);
        $this->_manager->flush();
    }

    public function resetPassword(Reader $reader)
    {
        // cleanup expired reset requests
        $this->cleanupExpiredReset();

        // set digest
        $resetDigest = hash('sha256',
            $reader->getId() . $reader->getEmail() . $reader->getPassword() . openssl_random_pseudo_bytes(64, $crypto_strong)
        );

        $reader->setResetDigest($resetDigest);

        $resetDigestDatetime = (new DateTime('now'))
            ->add(new DateInterval('PT10M'));

        $reader->setResetDigestDatetime($resetDigestDatetime);

        $this->_manager->persist($reader);
        $this->_manager->flush();

        return $reader;
    }

    public function createNewPassword()
    {
        return bin2hex(openssl_random_pseudo_bytes(10, $crypto_strong));
    }

    public function cleanupExpiredReset()
    {
        $expiredResetRequests = $this->_manager->getRepository('AppBundle:Reader')->findExpiredResetRequests();

        foreach($expiredResetRequests as $expiredResetRequest) {
            $this->_manager->remove($expiredResetRequest);
        }

        $this->_manager->flush();
    }

    public function cleanupFailedReset($readerId)
    {
        $failedResetRequest = $this->_manager->getRepository('AppBundle:Reader')->findOneBy($readerId);

        if( !$failedResetRequest )
            return;

        $this->_manager->remove($failedResetRequest);
        $this->_manager->flush();
    }
}
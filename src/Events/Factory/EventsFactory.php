<?php
namespace Events\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory de création du service d'evenements
 * @author dsuhard
 *
 */
class EventsFactory implements FactoryInterface
{
	/**
	 * (non-PHPdoc)
	 * @see Zend\ServiceManager.FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$eventManager = $serviceLocator->get('EventManager')->getSharedManager();

		$config = $serviceLocator->get('EventsConfig');

		if (!empty($config)) {
			foreach ($config as $identifier => $events) {
				foreach ($events as $eventName => $callbacks) {
					foreach ($callbacks as $callback) {
						if ($callback['service'] !== '' && $callback['function'] !== '') {
							if ($serviceLocator->has($callback['service'])) {
								$serv = $serviceLocator->get($callback['service']);

								if (!isset($callback['priority'])) {
									$eventManager->attach($identifier, $eventName, array($serv, $callback['function']));
								} else {
									$eventManager->attach($identifier, $eventName, array($serv, $callback['function']), $callback['priority']);
								}
							} else {
								error_log($callback['service'] . ' not defined service');
							}
						}
					}
				}
			}
		}
		return $this;
	}
}
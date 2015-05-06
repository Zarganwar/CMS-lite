<?php

namespace Eshop\DI;

use Kdyby;
use Kdyby\Doctrine\DI\IEntityProvider;
use Nette;
use Nette\Application\IPresenterFactory;

class EshopExtension extends Nette\DI\CompilerExtension implements IEntityProvider, \IMainMenuProvider {

	public function loadConfiguration() {
		$config = $this->loadFromFile(__DIR__ . '/config.neon');
		$containerBuilder = $this->getContainerBuilder();
		$this->compiler->parseServices($containerBuilder, $config);

		if ($config['console']) {
			foreach ($config['console']['commands'] as $i => $command) {
				$containerBuilder->addDefinition($this->prefix('cli.' . $i))
					->addTag(Kdyby\Console\DI\ConsoleExtension::COMMAND_TAG)
					->setInject(FALSE)// lazy injects
					->setClass($command);
			}
		}
	}

	public function beforeCompile() {
		$container = $this->getContainerBuilder();

		// presenter mapping configuration
		$container
			->getDefinition($container->getByType(IPresenterFactory::class) ?: 'nette.presenterFactory')
			->addSetup('setMapping', array(array('Eshop' => 'Eshop\\*Module\\Presenters\\*Presenter')));

		// presenter registration
		$robotLoader = new Nette\Loaders\RobotLoader();
		$robotLoader->addDirectory(__DIR__ . '/..');
		$robotLoader->setCacheStorage(new Nette\Caching\Storages\MemoryStorage());
		$robotLoader->rebuild();
		$counter = 0;
		foreach ($robotLoader->getIndexedClasses() as $class => $file) {
			try {
				$reflection = Nette\Reflection\ClassType::from($class);
				if (!$reflection->implementsInterface(Nette\Application\IPresenter::class)) {
					continue;
				}
				if (!$reflection->isInstantiable()) {
					continue;
				}
				$container->addDefinition($this->prefix(++$counter))
					->setClass($class)
					->setInject(TRUE)
					->setAutowired(FALSE)
					->addTag('nette.presenter', $class);
				//FIXME: invalidLinkMode
			} catch (\ReflectionException $e) {
				continue;
			}
		}

		// router setup (prepend)
		$router = $container->getDefinition($container->getByType(Nette\Application\IRouter::class) ?: 'router');
		$tmp = new Nette\Application\Routers\Route('eshop[/<presenter>[/<action>[/<id>]]]', array(
			'module' => 'Eshop',
			'presenter' => 'Category',
			'action' => 'default',
		));
		$router->addSetup('\App\RouterFactory::prependTo($service, ?)', [$tmp]);
	}

	/**
	 * Returns associative array of Namespace => mapping definition
	 *
	 * @return array
	 */
	public function getEntityMappings() {
		return [
			'Eshop' => __DIR__ . '/..',
		];
	}

	public function getMenuItems() {
		//TODO: SplPriorityQueue
		return [
			(new \MainMenuItem)
				->setTitle('Eshop Menu Item 1')
				->setLink(':Homepage:default'),
			(new \MainMenuItem)
				->setTitle('Eshop Menu Item 2')
				->setLink(':Eshop:Category:default'),
		];
	}

}

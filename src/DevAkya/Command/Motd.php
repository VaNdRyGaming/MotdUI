<?php

namespace DevAkya\Command;

use DevAkya\Main;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\Server;

use jojoe77777\FormAPI;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;

class Motd extends PluginCommand {

	const PREFIX = "§7[§6MOTD UI§7] ";
	const DESCRIPTION = "Définir un nouveau motd";

	/** @var Main $plugin */
	private $plugin;

	public function __construct(Main $plugin) {
		parent::__construct("motd", $plugin);
		$this->setDescription("Définir un nouveau motd.");
		$this->setPermission("set.motd.perm");
		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if($sender instanceof Player) {
			if($sender->hasPermission("set.motd.perm")) {
				$this->OpenMotdUI($sender);
			} else {
				$sender->sendMessage(self::PREFIX . "§cVous n'avez pas la permission.");
			}
		} else {
			$this->plugin->getLogger()->info("Error ...");
		}
	}

	public function OpenMotdUI(Player $sender) {
		$api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");

		if($api === null || $api->isDisabled()) {
			return true;
		}

		$form = $api->createSimpleForm(function(Player $sender, $data) {
			$result = $data;

			if($result === null) {
				return;
			}
			switch($result) {
				case 0: // QUITTER
					$sender->sendMessage(self::PREFIX . "§cMOTD n'a pas été changé.");
				break;

				case 1: // INFORMATION
					$this->OpenInformation($sender);
				break;

				case 2: // CHANGE MOTD
					$this->ChangeMOTD($sender);
				break;
			}
		});
		$leave = "https://gamepedia.cursecdn.com/minecraft_gamepedia/9/90/Structure_Void_JE2.png?version=0e9f8eac0d81da32d9a96cf3736f1e5b";
		$info = "https://gamepedia.cursecdn.com/minecraft_gamepedia/e/e7/Item_Frame_%28Item%29.png?version=d6382458409b204949cb256e5513721d";
		$change_motd = "https://gamepedia.cursecdn.com/minecraft_gamepedia/b/ba/Book_and_Quill.png?version=81a8d06a4179deca6b1107c0ded298c0";

		$form->setTitle(self::PREFIX);
		$form->addButton("§4Quitter", 1, $leave);
		$form->addButton("§5Information \n§dBy AkyaMc#1736", 1, $info);
		$form->addButton("Définir un nouveau \nmotd", 1, $change_motd);
		$form->sendToPlayer($sender);
	}

	public function OpenInformation(Player $sender) {
		$api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");

		if($api === null || $api->isDisabled()) {
			return true;
		}

		$form = $api->createSimpleForm(function(Player $sender, $data) {
			$result = $data;

			if($result === null) {
				return;
			}
			switch($result) {
				case 0: //Retour
					$this->OpenMotdUI($sender);
				break;
			}
		});
		$form->setTitle(self::PREFIX);
		$form->setContent(
			" \n " .
			" \n" .
			"Plugin:§a MotdUI \n" .
			"§rPermission:§a set.motd.perm \n" .
			"§rVersion:§a 1.0 \n" .
			"§rApi:§a 3.11.6 \n" .
			"§rGithub:§a DevAkya \n" .
			"§rAuteur:§a DevAkya " .
			" \n " .
			" \n " .
			" \n" .
			"§rPlugins requis: \n" .
			"§r-§a DevTools \n" .
			"§r-§a FormAPI " .
			" \n " .
			" \n " 
		);
		$form->addButton("§cRetour");
		$form->sendToPlayer($sender);
	}

	public function ChangeMOTD(Player $sender) {
		$api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");

		if($api === null || $api->isDisabled()) {
			return true;
		}

		$form = $api->createCustomForm(function(Player $sender, $data) {
			$result = $data;

			if($result != null) {
				$motd = $data[0];

				$this->plugin->getServer()->getNetwork()->setName($motd);
				$sender->sendMessage(self::PREFIX . "§aMOTD a été définie sur:§r \n " . $motd);
			}
		});
		$form->setTitle(self::PREFIX);
		$form->addInput(self::DESCRIPTION);
		$form->sendToPlayer($sender);
	}
}
<?php
declare(strict_types=1);
/**
 * ██╗░░██╗██╗██████╗░░█████╗░████████╗███████╗░█████╗░███╗░░░███╗
 * ██║░░██║██║██╔══██╗██╔══██╗╚══██╔══╝██╔════╝██╔══██╗████╗░████║
 * ███████║██║██████╔╝██║░░██║░░░██║░░░█████╗░░███████║██╔████╔██║
 * ██╔══██║██║██╔══██╗██║░░██║░░░██║░░░██╔══╝░░██╔══██║██║╚██╔╝██║
 * ██║░░██║██║██║░░██║╚█████╔╝░░░██║░░░███████╗██║░░██║██║░╚═╝░██║
 * ╚═╝░░╚═╝╚═╝╚═╝░░╚═╝░╚════╝░░░░╚═╝░░░╚══════╝╚═╝░░╚═╝╚═╝░░░░░╚═╝
 * FloatingTextApi By WillyDuGang
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/
 *
 *
 * GitHub: https://github.com/HiroshimaTeam/FloatingTextApi-HiroTeam
 */
namespace HiroTeam\FloatingTextApi;

use HiroTeam\FloatingTextApi\entity\FloatingTextEntity;
use HiroTeam\FloatingTextApi\utils\SkinTag;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class FloatingText extends PluginBase
{

    /**
     * @var SkinTag
     */
    private $skinTag;

    /**
     * @var FloatingText
     */
    private static $instance;

    public function onEnable()
    {
        $this->skinTag = new SkinTag();
        $this->getServer()->getPluginManager()->registerEvents(new FloatingTextListener(), $this);
        self::$instance = $this;
    }

    /**
     * @param Player $player
     * @param string $text
     * @return string
     */
    public function spawnFloatingText(Player $player, string $text): string
    {
        $level = $player->getLevel();
        $nbt = Entity::createBaseNBT(
            new Vector3(
            $player->getFloorX(),
            $player->getFloorY(),
            $player->getFloorZ()));
        $nbt->setTag(clone $this->skinTag->getSkinTag());
        $floatingText = new FloatingTextEntity($level, $nbt);
        $floatingText->initFloatingText();
        $floatingText->spawnToAll();
        $floatingText->setNameTag($text);
        return $floatingText->getFloatingTextId();
    }

    /**
     * @param string $text
     * @param string[]|string $id
     * @return void
     */
    public function updateFloatingTextById(string $text, $id): void
    {
        if (!is_array($id)) {
            $id = [$id];
        }
        foreach ($this->getServer()->getLevels() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof FloatingTextEntity) {
                    if(in_array($entity->getFloatingTextId(), $id)){
                        $entity->setNameTag($text);
                    }
                }
            }
        }
    }

    /**
     * @param Entity $entity
     * @return false|string
     */
    public function removeFloatingTextByEntity(Entity $entity)
    {
        if ($entity instanceof FloatingTextEntity) {
            $id = $entity->getFloatingTextId();
            $entity->kill();
            return $id;
        } else
            return false;
    }

    /**
     * @param string[]|string $id
     * @return void
     */
    public function removeFloatingTextById($id): void
    {
        if (!is_array($id)) {
            $id = [$id];
        }
        foreach ($this->getServer()->getLevels() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof FloatingTextEntity) {
                    if(in_array($entity->getFloatingTextId(), $id)){
                        $entity->kill();
                    }
                }
            }
        }
    }

    /**
     * @return FloatingText
     */
    public static function getInstance(): FloatingText
    {
        return self::$instance;
    }
}
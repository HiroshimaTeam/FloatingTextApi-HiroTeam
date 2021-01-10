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

    public function onEnable()
    {
        $this->skinTag = new SkinTag();
        $this->getServer()->getPluginManager()->registerEvents(new FloatingTextListener(), $this);
    }

    /**
     * @param Player $player
     * @param string $text
     * @return string
     */
    public function spawnFloatingText(Player $player, string $text): string
    {
        $x = intval($player->getX());
        $y = intval($player->getY());
        $z = intval($player->getZ());
        $level = $player->getLevel();
        $levelName = $level->getName();
        $nbt = Entity::createBaseNBT(new Vector3($x, $y, $z));
        $nbt->setTag(clone $this->skinTag->getSkinTag());
        $floatingText = new FloatingTextEntity($level, $nbt);
        $floatingText->spawnToAll();
        $floatingText->setImmobile();
        $floatingText->setNameTag($text);
        return "$x:$y:$z:$levelName";
    }

    /**
     * @param string $text
     * @param string[]|string $xyzLevel
     * @return void
     */
    public function updateFloatingTextByPos(string $text, $xyzLevel): void
    {
        if (!is_array($xyzLevel)) {
            $xyzLevel = [$xyzLevel];
        }
        foreach ($this->getServer()->getLevels() as $level) {
            $levelName = $level->getName();
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof FloatingTextEntity) {
                    $x = $entity->getX();
                    $y = $entity->getY();
                    $z = $entity->getZ();
                    if (in_array("$x:$y:$z:$levelName", $xyzLevel)) {
                        $entity->setNameTag($text);
                    }
                }
            }
        }
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function removeOneFloatingText(Entity $entity): bool
    {
        if ($entity instanceof FloatingTextEntity) {
            $entity->kill();
            return true;
        } else
            return false;
    }

    /**
     * @param string[]|string $xyzLevel
     * @return void
     */
    public function removeFloatingTextByPos($xyzLevel): void
    {
        if (!is_array($xyzLevel)) {
            $xyzLevel = [$xyzLevel];
        }
        foreach ($this->getServer()->getLevels() as $level) {
            $levelName = $level->getName();
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof FloatingTextEntity) {
                    $x = $entity->getX();
                    $y = $entity->getY();
                    $z = $entity->getZ();
                    if (in_array("$x:$y:$z:$levelName", $xyzLevel)) {
                        $entity->kill();
                    }
                }
            }
        }
    }
}

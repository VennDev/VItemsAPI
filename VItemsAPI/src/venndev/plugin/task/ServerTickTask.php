<?php

/**
 * All Rights Reserved
 *
 * Copyright (c) 2024 VennDev (https://github.com/VennDev)
 *
 * THE CONTENTS OF THIS PROJECT ARE PROPRIETARY AND CONFIDENTIAL.
 * UNAUTHORIZED COPYING, TRANSFERRING OR REPRODUCTION OF THE CONTENTS OF THIS PROJECT, VIA ANY MEDIUM IS STRICTLY PROHIBITED.
 *
 * The receipt or possession of the source code and/or any parts thereof does not convey or imply any right to use them
 * for any purpose other than the purpose for which they were provided to you.
 *
 * The software is provided "AS IS", without warranty of any kind, express or implied, including but not limited to
 * the warranties of merchantability, fitness for a particular purpose and non infringement.
 * In no event shall the authors or copyright holders be liable for any claim, damages or other liability,
 * whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software
 * or the use or other dealings in the software.
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 */

declare(strict_types=1);

namespace venndev\plugin\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use venndev\plugin\manager\StatsManager;

final class ServerTickTask extends Task
{

    public function __construct(public Server $server)
    {
        //TODO: Add a check to make sure the server is running VItemsAPI
    }

    /**
     * @return void
     */
    public function onRun(): void
    {
        foreach ($this->server->getOnlinePlayers() as $player) {
            $statsManager = new StatsManager($player);
            if ($statsManager->hasJoined()) {
                $statsManager->updateInventoryStats();
                $statsManager->updateStats();
            }
        }
    }

}
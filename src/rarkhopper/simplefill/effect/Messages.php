<?php

declare(strict_types = 1);

namespace rark\simple_fill\effect;

use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function number_format;

abstract class Messages {
    final private function __construct() {/** NOOP */
    }
    const PREFIX = TextFormat::BOLD . TextFormat::WHITE . '[SIMPLE FILL] ' . TextFormat::RESET;
    const SET_POS1 = TextFormat::YELLOW . '基点を設定しました。';
    const SET_POS2 = TextFormat::YELLOW . '終点を設定しました。';
    const TURN_ON = TextFormat::GREEN . 'Fill Modeを有効化しました。';
    const TURN_OFF = TextFormat::RED . 'Fill Modeを無効化しました。';
    const ADDED_ITEMS = TextFormat::YELLOW . 'アイテムを付与しました';
    const START_FILL = TextFormat::GREEN . 'fillを開始します。';
    const UNDO = '回分の操作を取り消しました。';
    const PLZ_EXEC_IN_GAME = TextFormat::RED . 'ゲーム内で実行してください';
    const ERR_CONTAINER = TextFormat::RED . 'Containerを取得できませんでした';
    const ERR_COUNT = TextFormat::RED . 'Undoのカウントは1以上である必要があります';
    const ERR_SIZE = TextFormat::RED . 'Fillのサイズが大きすぎます';
    const OVER_CMD_ARG = TextFormat::RED . 'コマンドの引数が多すぎます';

    public static function getUndoMessage(int $count) : string {
        return TextFormat::GREEN . number_format($count) . self::UNDO;
    }

    public static function sendMessage(Player $player, string $txt) : bool {
        try {
            $session = $player->getNetworkSession();
        } catch(\Exception) {
            return false;
        }
        $session->sendDataPacket(TextPacket::raw(self::PREFIX . $txt));
        return true;
    }
}

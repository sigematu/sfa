<?php

// ステータス
define("STATUS_INACTIVE", 0); // 無効
define("STATUS_ACTIVE", 1); // 有効

// 売上ランク
define("SALES_RANK_S", 1); // S(1,000億円〜)
define("SALES_RANK_A", 2); // A(300〜1,000億円)
define("SALES_RANK_B", 3); // B(100〜300億円)
define("SALES_RANK_C", 4); // C(30〜100億円)
define("SALES_RANK_D", 5); // D(〜30億円)

// 振込手数料
define("FEE_OUR", 1); // 当方負担
define("FEE_OTHER", 2); // 先方負担

// 分単位
define("MIN_1", 1); // 1分
define("MIN_5", 2); // 5分
define("MIN_10", 3); // 10分
define("MIN_15", 4); // 15分
define("MIN_30", 5); // 30分

// 精算区分
define("PERIOD_NORMAL", 1); // 通常
define("PERIOD_HOURLY", 2); // 時給

// 契約形態
define("CONTRACT_SERVICE", 1); // 準委任
define("CONTRACT_FIXED", 2); // 請負
define("CONTRACT_DISPATCH", 3); // 派遣

// 顧客注文メールのステータス
define("CL_ORDER_MAIL_NOT_YET", 1);         // まだ/まだ
define("CL_ORDER_MAIL_SENT", 2);            // ○/まだ
define("CL_ORDER_MAIL_DONE", 3);            // ○/○
define("CL_ORDER_MAIL_CLIENT_SPEC", 4);     // 注文メール受領(顧客仕様)

// 顧客注文書のステータス
define("CL_ORDER_NOT_YET", '1'); // まだ/まだ
define("CL_ORDER_RECEIVED_BY_MAIL", '2'); // 電子受領/まだ
define("CL_ORDER_SENT_BY_MAIL", '3'); // 電子受領/電子送付
define("CL_ORDER_UNNECESSARY_BY_MAIL", '4'); // 電子受領/不要
define("CL_ORDER_RECEIVED_BY_EDI", '5'); // 電子受領/まだ(EDI)
define("CL_ORDER_SENT_BY_EDI", '6'); // 電子受領/電子送付(EDI)
define("CL_ORDER_UNNECESSARY_BY_EDI", '7'); // 電子受領/不要(EDI)
define("CL_ORDER_RECEIVED_BY_ORIGINAL", '8'); // 原本受領/まだ
define("CL_ORDER_SENT_BY_ORIGINAL", '9'); // 原本受領/原本送付
define("CL_ORDER_UNNECESSARY_BY_ORIGINAL", '10'); // 原本受領/不要
define("CL_ORDER_RECEIVED_UNNECESSARY_DISPATCH", '11'); // 電子受領/不要(派遣個別契約書)
define("CL_ORDER_SENT_UNNECESSARY_DISPATCH", '12'); // 電子送付/不要(派遣個別契約書)

// BP注文書のステータス
define("BP_ORDER_NOT_YET", '1'); // まだ/まだ
define("BP_ORDER_SENT", '2'); // 電子送付/まだ
define("BP_ORDER_RECEIVED", '3'); // 電子送付/電子受領
define("BP_ORDER_SENT_UNNECESSARY_DISPATCH", '4'); // 電子送付/不要(派遣個別契約書)
define("BP_ORDER_RECEIVED_UNNECESSARY_DISPATCH", '5'); // 電子受領/不要(派遣個別契約書)

// 所属
define("BELONG_OUR", 1); // 正社員
define("BELONG_BP", 2); // BP

// 部署
define("DEPT_BS", 1); // BS部
define("DEPT_NS", 2); // NS部

// 役職
define("POS_CEO", 1); // 社長・代表
define("POS_EXECUTIVE", 2); // 役員級
define("POS_DEPARTMENT_HEAD", 3); // 部長級
define("POS_SECTION_MANAGER", 4); // 次長・課長級
define("POS_TEAM_LEADER", 5); // 主任級
define("POS_STAFF", 6); // 一般職

// 職種
define("JOB_SALES", 'sales'); // 営業
define("JOB_NON_SALES", 'non-sales'); // 営業以外
define("JOB_ACCOUNTING", 'accounting'); // 経理

// スキルシート
define("SKILL_SHEET_NOT_UPLOADED", '0'); // アップロードされていない
define("SKILL_SHEET_UPLOADED", '1'); // アップロードされている

// 書類の回収、ファイリング
define("NOT_COLLECTED", '0'); // 回収まだ
define("COLLECTED", '1'); // 回収済み
define("FILING", '2'); // ファイリング完了

// 再営業
define("REOPEN_OK", '1'); // 再営業可
define("REOPEN_PENDING", '2'); // 再営業保留
define("REOPEN_NG", '3'); // 再営業不可

// 契約更新フラグ
define("CONTRACT_NEW", 0); // 新規契約
define("CONTRACT_RENEW", 1); // 更新契約

// 顧客ステータス（契約更新管理）
define("RENEWAL_CLIENT_STATUS_CONTINUING_PROSPECT", 1); // 継続見込み
define("RENEWAL_CLIENT_STATUS_CUSTOMER_CONTINUED", 2);  // 顧客継続
define("RENEWAL_CLIENT_STATUS_CONTINUE", 3);            // 継続
define("RENEWAL_CLIENT_STATUS_NEGOTIATING", 4);         // 協議中
define("RENEWAL_CLIENT_STATUS_UNIT_PRICE", 5);          // 単価交渉
define("RENEWAL_CLIENT_STATUS_ENDED", 6);               // 終了
define("RENEWAL_CLIENT_STATUS_ENDING_PROSPECT", 7);     // 終了見込み
define("RENEWAL_CLIENT_STATUS_LABELS", [
    RENEWAL_CLIENT_STATUS_CONTINUING_PROSPECT => '継続見込み',
    RENEWAL_CLIENT_STATUS_CUSTOMER_CONTINUED  => '顧客継続',
    RENEWAL_CLIENT_STATUS_CONTINUE            => '継続',
    RENEWAL_CLIENT_STATUS_NEGOTIATING         => '協議中',
    RENEWAL_CLIENT_STATUS_UNIT_PRICE          => '単価交渉',
    RENEWAL_CLIENT_STATUS_ENDED               => '終了',
    RENEWAL_CLIENT_STATUS_ENDING_PROSPECT     => '終了見込み',
]);

// 経費精算の税区分
define("TAX_INCLUSIVE", 1); // 内税
define("TAX_EXCLUSIVE", 2); // 外税
define("TAX_NONE", 3); // 非課税
define("TAX_TYPE_LABELS", [
    TAX_INCLUSIVE => '内税',
    TAX_EXCLUSIVE => '外税',
    TAX_NONE => '非課税',
]);

// 消費税率
define("TAX_RATE", 0.1); // 消費税率（10%）

// 注視エンジニア 事由
define("WATCHLIST_REASON_ATTENDANCE",     1); // 勤怠不良
define("WATCHLIST_REASON_SKILL",          2); // スキル不足
define("WATCHLIST_REASON_COMMUNICATION",  3); // コミュニケーション不良
define("WATCHLIST_REASON_ATTITUDE",       4); // 勤務態度
define("WATCHLIST_REASON_HIGH_WORKLOAD",  5); // 高稼働
define("WATCHLIST_REASON_OTHER",          6); // そのほか
define("WATCHLIST_REASON_LABELS", [
    WATCHLIST_REASON_ATTENDANCE    => '勤怠不良',
    WATCHLIST_REASON_SKILL         => 'スキル不足',
    WATCHLIST_REASON_COMMUNICATION => 'コミュニケーション不良',
    WATCHLIST_REASON_ATTITUDE      => '勤務態度',
    WATCHLIST_REASON_HIGH_WORKLOAD => '高稼働',
    WATCHLIST_REASON_OTHER         => 'そのほか',
]);

// 注視エンジニア 対応フラグ
define("WATCHLIST_FLAG_URGENT",  1); // 要対応
define("WATCHLIST_FLAG_WATCH",   2); // 注視
define("WATCHLIST_FLAG_OBSERVE", 3); // 経過観察
define("WATCHLIST_FLAG_LABELS", [
    WATCHLIST_FLAG_URGENT  => '要対応',
    WATCHLIST_FLAG_WATCH   => '注視',
    WATCHLIST_FLAG_OBSERVE => '経過観察',
]);

// 注視エンジニア 起点
define("WATCHLIST_ORIGIN_CLIENT",   1); // 顧客
define("WATCHLIST_ORIGIN_OUR",      2); // 当社
define("WATCHLIST_ORIGIN_BP",       3); // BP
define("WATCHLIST_ORIGIN_ENGINEER", 4); // エンジニア
define("WATCHLIST_ORIGIN_SELF",     5); // 本人
define("WATCHLIST_ORIGIN_LABELS", [
    WATCHLIST_ORIGIN_CLIENT   => '顧客',
    WATCHLIST_ORIGIN_OUR      => '当社',
    WATCHLIST_ORIGIN_BP       => 'BP',
    WATCHLIST_ORIGIN_ENGINEER => 'エンジニア',
    WATCHLIST_ORIGIN_SELF     => '本人',
]);

// 連絡手段
define("CONTACT_METHOD_PHONE", 1); // 電話
define("CONTACT_METHOD_EMAIL", 2); // メール
define("CONTACT_METHOD_WEB",   3); // Web会議
define("CONTACT_METHOD_CHAT",  4); // チャット
define("CONTACT_METHOD_SMS",   5); // SMS
define("CONTACT_METHOD_OTHER", 6); // その他
define("CONTACT_METHOD_LABELS", [
    CONTACT_METHOD_PHONE => '電話',
    CONTACT_METHOD_EMAIL => 'メール',
    CONTACT_METHOD_WEB   => 'Web会議',
    CONTACT_METHOD_CHAT  => 'チャット',
    CONTACT_METHOD_SMS   => 'SMS',
    CONTACT_METHOD_OTHER => 'その他',
]);

// 請求入力種別
define("INPUT_TYPE_DECIMAL", 1); // 小数点（時間）入力
define("INPUT_TYPE_HM", 2);      // 時分入力

// 支払サイト
define("PAYMENT_SITE_BIMONTHLY", 40);    // サイト40日（翌々月10日払い）
define("PAYMENT_SITE_BIMONTHLY_DAY", 10); // 翌々月の支払日

// Excelシリアル日付変換
define("SECONDS_PER_DAY", 86400);   // 1日の秒数
define("EXCEL_DATE_OFFSET", 25569); // UnixエポックとExcelエポックの差（日数）

// URL
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    define("URL_BASE", 'http://localhost/crm/'); // ベースURL
} else {
    define("URL_BASE", 'https://app3.icz.co.jp/crm/'); // ベースURL
}
define("URL_CONTRACT", 'https://prj2.icz.co.jp/projects/scs/wiki/%E5%8F%82%E7%94%BB%E6%B1%BA%E5%AE%9A%E6%99%82%E3%81%AETODO'); // 参画決定時のTODO

// Slack URL
define("SLACK_SALES_ALL", 'https://app.slack.com/client/T0XCWCXDZ/C09HNK0K4SK');
define("SLACK_SALES_ALL_TASK", 'https://app.slack.com/client/T0XCWCXDZ/C09J406636E');
define("SLACK_SALES_JIMU", 'https://app.slack.com/client/T0XCWCXDZ/G01PNDSD0E6');

// Slack Channel ID
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    define("SLACK_SALES_ALL_ID", 'C0ARZUED2AU');
    define("SLACK_SALES_ALL_TASK_ID", 'C0ARUFAC50V');
    define("SLACK_SALES_JIMU_ID", 'C0ARUFAC50V');
} else {
    define("SLACK_SALES_ALL_ID", 'C09HNK0K4SK');
    define("SLACK_SALES_ALL_TASK_ID", 'C09J406636E');
    define("SLACK_SALES_JIMU_ID", 'G01PNDSD0E6');
}

// Slack Flag
define("SLACK_FLAG_THREAD", 1); // スレッドを立てる
define("SLACK_FLAG_REPLY", 2); // スレッドに返信する

// ユーザID
define("USER_ENOMOTO", 2); // 榎本
define("USER_SAKAMOTO", 3); // 坂本
define("USER_UCHIDA", 4); // 内田
define("USER_TAKEDA", 5); // 武田
define("USER_SHIIBASHI", 12); // 椎橋
define("USER_TAKASE", 13); // 髙瀨
define("USER_OGAWA_M", 14); // 小川
define("USER_TANIMICHI", 15); // 谷道

// カラー設定
define("COLORS", [
	'ENOMOTO' => [
		'background' => 'rgba(54, 162, 235, 0.2)',
		'border' => 'rgba(54, 162, 235, 1)'
	],
	'TAKEDA' => [
		'background' => 'rgba(255, 99, 132, 0.2)',
		'border' => 'rgba(255, 99, 132, 1)'
	],
	'SHIIBASHI' => [
		'background' => 'rgba(255, 206, 86, 0.2)',
		'border' => 'rgba(255, 206, 86, 1)'
	],
	'TAKASE' => [
		'background' => 'rgba(75, 192, 192, 0.2)',
		'border' => 'rgba(75, 192, 192, 1)'
	],
	'OGAWA_M' => [
		'background' => 'rgba(255, 145, 0, 0.2)',
		'border' => 'rgb(230, 123, 53)'
	],
	'TANIMICHI' => [
		'background' => 'rgba(108, 117, 125, 0.2)',
		'border' => 'rgb(108, 117, 125)'
	],
]);

// メール通知
define("MAIL_FROM_ADDRESS", 'admin@icz.co.jp'); // 送信元アドレス
define("MAIL_FROM_NAME", '[iCZ CRM] システム'); // 送信元名
define("MAIL_TO", 'admin@icz.co.jp'); // 送信先アドレス
define("MAIL_SUBJECT", '[iCZ CRM] 通知'); // 件名

// 一覧での表示制御
define("TRUNCATE_LENGTH", 10); // 省略文字数
define("TRUNCATE_ELLIPSIS", '...'); // 省略文字

// 契約(月次)の表示条件
define("COND_MONTH", '&sort=Engineers.emp_no&direction=asc&limit=200'); // ソート条件、1ページの件数

return [];

<?php

namespace App;

class ServiceRequestStatus
{
    const PENDING = 0; // 保留
    const IN_PROGRESS = 1; // 承認/進行中
    const COMPLETED = 2; // 完了
    const DELETED = 3; // 削除/キャンセル/拒否
}
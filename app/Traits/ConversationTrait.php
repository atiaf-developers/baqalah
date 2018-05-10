<?php

namespace App\Traits;

use App\Models\Conversation;
use App\Models\ConversationReply;
use Carbon\Carbon;
use DB;

trait ConversationTrait {

    public function addMessage($user_id, $friend_id, $reply) {
        $Conversation = $this->findConversation($user_id, $friend_id);
        //dd($Conversation);
        if ($Conversation != null) {
            try {
                if ($Conversation->deleted_by != null) {
                    $Conversation->deleted_by = null;
                    $Conversation->save();
                }
                $ConversationReply = new ConversationReply;
                $ConversationReply->user_id = $user_id;
                $ConversationReply->reply = $reply;
                $ConversationReply->is_read = 0;
                $ConversationReply->conversation_id = $Conversation->id;
                $ConversationReply->save();
                return _api_json('', ['message' => _lang('app.message_sent_successfully')]);
            } catch (Exception $ex) {
                return _api_json('', ['message' => _lang('app.error_is_occured')]);
            }

            //return $friendship;
        } else {
            DB::beginTransaction();
            try {
                $Conversation = new Conversation;
                $Conversation->user_one_id = $user_id;
                $Conversation->user_two_id = $friend_id;
                $Conversation->save();
                $ConversationReply = new ConversationReply;
                $ConversationReply->user_id = $user_id;
                $ConversationReply->reply = $reply;
                $ConversationReply->is_read = 0;
                $ConversationReply->conversation_id = $Conversation->id;
                $ConversationReply->save();
                DB::commit();
                return _api_json('', ['message' => _lang('app.message_sent_successfully')]);
            } catch (Exception $ex) {
                DB::rollback();
                return _api_json('', ['message' => _lang('app.error_is_occured')]);
            }

            //return $friendship;
        }
    }

    public function findConversation($user_id, $friend_id) {

        return Conversation::where(function($query) use($user_id) {
                            $query->where('user_one_id', $user_id)
                            ->orWhere('user_two_id', $user_id);
                        })
                        ->where(function ($query) use($friend_id) {
                            $query->where('user_one_id', $friend_id)
                            ->orWhere('user_two_id', $friend_id);
                        })
                        ->first();
    }

    public function getConversations($user_id, $limit = null, $offset = null) {
        return $this->findConversations($user_id, $limit, $offset);
    }

    public function getConversationMessages($conversation_id, $user_id, $limit = null, $offset = null) {

        $Conversation = Conversation::join('conversation_reply as R', 'R.conversation_id', '=', 'conversation.id');
        $Conversation->join('users as U', 'R.user_id', '=', 'U.id');
        $Conversation->where('conversation.id', $conversation_id);
        $Conversation->where(function($query) use($user_id) {
            $query->where('R.deleted_by', '!=', $user_id)
                    ->orWhereRaw('R.deleted_by is null');
        });
        $Conversation->orderBy('R.created_at', 'desc');
        $Conversation->select('R.id', 'U.id as user_id', 'U.username', 'R.reply', 'U.image', 'R.created_at');
        if (!empty($offset)) {
            $Conversation->offset($offset);
        }
        if (!empty($limit)) {
            //dd($limit);
            $Conversation->limit($limit);
        }

        return $Conversation->get();
    }

    public static function unReadMessages($user_id) {

        $Conversation = Conversation::join('conversation_reply as R', 'R.conversation_id', '=', 'conversation.id');
        $Conversation->join('users as U', 'R.user_id', '=', 'U.id');
        $Conversation->where('R.user_id', '!=', $user_id);
        $Conversation->where(function($query) use($user_id) {
            $query->where('conversation.user_one_id', $user_id);
            $query->orWhere('conversation.user_two_id', $user_id);
        });
        $Conversation->where('R.is_read', 0);
        $Conversation->where(function($query) use($user_id) {
            $query->where('R.deleted_by', '!=', $user_id);
            $query->orWhereRaw('R.deleted_by is null');
        });
        return $Conversation->count();
    }

    public static function unReadMessages2($user_id) {

        $Conversation = Conversation::join('conversation_reply as R', 'R.conversation_id', '=', 'conversation.id');
        $Conversation->join('users as U', 'R.user_id', '=', 'U.id');
        $Conversation->where('U.id', $user_id);
        $Conversation->where('R.is_read', 0);
        $Conversation->where(function($query) use($user_id) {
            $query->where('R.deleted_by', '!=', $user_id);
            $query->orWhereRaw('R.deleted_by is null');
        });
        return $Conversation->count();
    }

    public function markAsReadMessage($conversation_id, $user_id, $friend_id = false) {
        $ConversationReply = DB::table('conversation_reply')->join('conversation', 'conversation_reply.conversation_id', '=', 'conversation.id');
        $ConversationReply->timestamps = false;
        $ConversationReply->where('conversation_reply.is_read', 0);
        $ConversationReply->where('conversation_reply.user_id', '!=', $user_id);
        $ConversationReply->where(function($query) use($user_id) {
            $query->where('conversation_reply.deleted_by', '!=', $user_id);
            $query->orWhereRaw('conversation_reply.deleted_by is null');
        });
        if ($conversation_id) {
            $ConversationReply->where('conversation.id', $conversation_id);
        } else {
            $ConversationReply->where(function($query) use($user_id) {
                        $query->where('conversation.user_one_id', $user_id);
                        $query->orWhere('conversation.user_two_id', $user_id);
                    })
                    ->where(function ($query) use($friend_id) {
                        $query->where('conversation.user_one_id', $friend_id);
                        $query->orWhere('conversation.user_two_id', $friend_id);
                    });
        }
        $ConversationReply->update(['conversation_reply.is_read' => 1, 'conversation_reply.updated_at' => Carbon::now()]);
    }

    public function getConversationMessages3($conversation_id, $user_id, $limit = null, $offset = null) {

        $Conversation = Conversation::join('conversation_reply as R', 'R.conversation_id', '=', 'conversation.id')
                ->join('users as U', 'R.user_id', '=', 'U.id')
                ->where('conversation.id', $conversation_id)
                ->where(function($query) use($user_id) {
                    $query->where('R.deleted_by', '!=', $user_id)
                    ->orWhereRaw('R.deleted_by is null');
                })
                ->orderBy('R.created_at', 'desc')
                ->select('R.id', 'U.id as user_id', 'U.username', 'R.reply', 'U.image', 'R.created_at');
        if (!empty($offset)) {
            $Conversation->offset($offset);
        }
        if (!empty($limit)) {
            //dd($limit);
            $Conversation->limit($limit);
        }

        return $Conversation->get();
    }

    public function getConversationMessages2($conversation_id, $limit = null, $offset = null) {

        $Conversation = Conversation::join('conversation_reply as R', 'R.conversation_id', '=', 'conversation.id')
                ->join('users as U', 'R.user_id', '=', 'U.id')
                ->where('conversation.id', $conversation_id)
                ->orderBy('R.created_at', 'desc')
                ->select('R.id', 'U.id as user_id', 'U.username', 'R.reply', 'U.image', 'R.created_at');
        if (!empty($offset)) {
            $Conversation->offset($offset);
        }
        if (!empty($limit)) {
            //dd($limit);
            $Conversation->limit($limit);
        }

        return $Conversation->get();
    }

    public function getConversationMessagesByUser($user_id, $friend_id, $limit = null, $offset = null) {

        $Conversation = Conversation::join('conversation_reply as R', 'R.conversation_id', '=', 'conversation.id')
                ->join('users as U', 'R.user_id', '=', 'U.id')
                ->where(function($query) use($user_id) {
                    $query->where('user_one_id', $user_id)
                    ->orWhere('user_two_id', $user_id);
                })
                ->where(function ($query) use($friend_id) {
                    $query->where('user_one_id', $friend_id)
                    ->orWhere('user_two_id', $friend_id);
                })
                ->where(function($query) use($user_id) {
                    $query->where('R.deleted_by', '!=', $user_id)
                    ->orWhereRaw('R.deleted_by is null');
                })
                ->orderBy('R.created_at', 'desc')
                ->select('R.id', 'U.id as user_id', 'U.username', 'R.reply', 'U.image', 'R.created_at');
        if (!empty($offset)) {
            $Conversation->offset($offset);
        }
        if (!empty($limit)) {
            //dd($limit);
            $Conversation->limit($limit);
        }

        return $Conversation->get();
    }

    public function getConversationMessagesByUser2($user_id, $friend_id, $limit = null, $offset = null) {

        $Conversation = Conversation::join('conversation_reply as R', 'R.conversation_id', '=', 'Conversation.id')
                ->join('users as U', 'R.user_id', '=', 'U.id')
                ->where(function($query) use($user_id) {
                    $query->where('user_one_id', $user_id)
                    ->orWhere('user_two_id', $user_id);
                })
                ->where(function ($query) use($friend_id) {
                    $query->where('user_one_id', $friend_id)
                    ->orWhere('user_two_id', $friend_id);
                })
                ->orderBy('R.created_at', 'desc')
                ->select('R.id', 'U.id as user_id', 'U.username', 'R.reply', 'U.image', 'R.created_at');
        if (!empty($offset)) {
            $Conversation->offset($offset);
        }
        if (!empty($limit)) {
            //dd($limit);
            $Conversation->limit($limit);
        }

        return $Conversation->get();
    }

    private function findConversations($user_id, $limit, $offset) {
        $sql = "SELECT C.id,(select reply from conversation_reply r where r.conversation_id =C.id order by created_at desc limit 1) as reply,
            (select is_read from conversation_reply r where r.conversation_id =C.id order by created_at desc limit 1) as is_read,
            (select created_at from conversation_reply r where r.conversation_id =C.id order by created_at desc limit 1) as created_at,
            U.id as user_id,U.image, U.username FROM 
            conversation as C  left join users as U on
            CASE
            WHEN C.user_one_id = '$user_id'
            THEN C.user_two_id = U.id 
            WHEN C.user_two_id= '$user_id'
            THEN C.user_one_id= U.id 
            END
            inner join conversation_reply as R on C.id=R.conversation_id
            WHERE (C.user_one_id ='$user_id' OR C.user_two_id ='$user_id')
            AND (C.deleted_by !='$user_id' OR C.deleted_by IS NULL)     
            GROUP BY C.id
            ORDER BY R.created_at desc
        ";
//        $sql .= " AND C.id=R.conversation_id";
//        $sql .= " AND (C.user_one_id ='$user_id' OR C.user_two_id ='$user_id')";
//        //$sql .= " AND C.deleted_by !='$user_id'";
//        $sql .= " GROUP BY C.id";
//        $sql .= " ORDER BY R.created_at desc";
////        $sql .= " ORDER BY C.id DESC";
        if (!empty($limit)) {
            //dd($limit);
            $sql .= " limit $limit";
        }
        if (!empty($offset)) {
            $sql .= " offset $offset";
        }


        $query = DB::select(DB::raw($sql));


        return $query;
    }

    private function findConversations2($user_id, $limit, $offset) {
        $sql = "SELECT C.id,(select reply from conversation_reply r where r.conversation_id =C.id order by created_at desc limit 1) as reply,
            (select is_read from conversation_reply r where r.conversation_id =C.id order by created_at desc limit 1) as is_read,
            (select created_at from conversation_reply r where r.conversation_id =C.id order by created_at desc limit 1) as created_at,
            U.id as user_id,U.image, U.username FROM users U, conversation C,conversation_reply R WHERE
        CASE

        WHEN C.user_one_id = '$user_id'
        THEN C.user_two_id = U.id
        WHEN C.user_two_id= '$user_id'
        THEN C.user_one_id= U.id
        END
        ";
        $sql .= " AND C.id=R.conversation_id";
        $sql .= " AND (C.user_one_id ='$user_id' OR C.user_two_id ='$user_id')";
        $sql .= " AND C.deleted_by !='$user_id'";
        $sql .= " GROUP BY C.id";
        $sql .= " ORDER BY R.created_at desc";
//        $sql .= " ORDER BY C.id DESC";
        if (!empty($limit)) {
            //dd($limit);
            $sql .= " limit $limit";
        }
        if (!empty($offset)) {
            $sql .= " offset $offset";
        }


        $query = DB::select(DB::raw($sql));


        return $query;
    }

}

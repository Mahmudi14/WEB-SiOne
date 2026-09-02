import type { Question } from '@/types/question'

export type RoomStatus =
    | 'locked'
    | 'waiting'
    | 'running'
    | 'finished'

export type GamePhase =
    | 'ready'
    | 'open'
    | 'result'
    | null

export type RoundResult =
    | 'normal'
    | 'no_elimination'

/*
|--------------------------------------------------------------------------
| Basic Room
|--------------------------------------------------------------------------
*/

export interface Room {
    id: number
    name: string
    code: string
    description: string | null

    status: RoomStatus
    status_label: string

    shuffle_questions: boolean

    created_at?: string
    updated_at?: string
}

/*
|--------------------------------------------------------------------------
| Room Form
|--------------------------------------------------------------------------
*/

export interface RoomFormData {
    name: string
    code: string
    description: string
    shuffle_questions: boolean
}

/*
|--------------------------------------------------------------------------
| Question Pagination
|--------------------------------------------------------------------------
*/

export interface QuestionPaginationLink {
    url: string | null
    label: string
    active: boolean
}

export interface PaginatedQuestions {
    current_page: number

    data: Question[]

    first_page_url: string
    from: number | null

    last_page: number
    last_page_url: string

    links: QuestionPaginationLink[]

    next_page_url: string | null

    path: string

    per_page: number

    prev_page_url: string | null

    to: number | null

    total: number
}

export interface QuestionFilters {
    search: string
}

/*
|--------------------------------------------------------------------------
| Editable / Viewable Room
|--------------------------------------------------------------------------
*/

export interface EditableRoom extends Room {
    can_edit: boolean
    can_edit_questions: boolean

    question_count: number

    questions: PaginatedQuestions

    question_filters: QuestionFilters
}

/*
|--------------------------------------------------------------------------
| Play Question
|--------------------------------------------------------------------------
*/

export interface PlayQuestionOption {
    id: number

    label:
        | 'A'
        | 'B'
        | 'C'
        | 'D'

    text: string
    is_correct: boolean
}

export interface PlayQuestion {
    id: number
    text: string
    game_order: number
    options: PlayQuestionOption[]
}

/*
|--------------------------------------------------------------------------
| Result
|--------------------------------------------------------------------------
*/

export interface CorrectParticipant {
    id: number
    name: string
    code: string
}

export interface QuestionResult {
    round_result: RoundResult

    answered_count: number
    correct_count: number
    wrong_count: number
    no_answer_count: number

    eliminated_count: number
    remaining_count: number

    correct_participants:
        CorrectParticipant[]
}

/*
|--------------------------------------------------------------------------
| Admin Play Room
|--------------------------------------------------------------------------
*/

export interface PlayRoom {
    id: number
    name: string
    code: string

    status: RoomStatus
    status_label: string

    game_phase: GamePhase

    shuffle_questions: boolean

    revision: number

    question_count: number

    participant_count: number
    active_participant_count: number
    eliminated_participant_count: number
    winner_count: number

    answered_count: number
    waiting_answer_count: number

    has_next_question: boolean

    current_question:
        | PlayQuestion
        | null

    result:
        | QuestionResult
        | null
}

/*
|--------------------------------------------------------------------------
| Admin Polling State
|--------------------------------------------------------------------------
*/

export interface AdminRoomState {
    revision: number

    status: RoomStatus

    game_phase: GamePhase

    participant_count: number
    active_participant_count: number
    eliminated_participant_count: number
    winner_count: number

    answered_count: number
    waiting_answer_count: number
}
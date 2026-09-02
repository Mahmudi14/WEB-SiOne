export type ResultRoundType =
    | 'normal'
    | 'no_elimination'

export interface ResultRoom {
    id: number
    name: string
    code: string
    description: string | null
    status: 'finished'
    status_label: string

    started_at: string | null
    finished_at: string | null
}

export interface ResultSummary {
    participant_count: number
    winner_count: number
    eliminated_count: number
    questions_used: number
}

export interface ResultParticipant {
    id: number
    name: string
    code: string
}

export interface EliminatedParticipant {
    id: number
    name: string
    code: string

    eliminated_at_question: {
        number: number
        text: string
    } | null
}

export interface ResultQuestion {
    id: number
    number: number
    text: string

    round_result:
        ResultRoundType

    participants_before: number

    answered_count: number
    correct_count: number
    wrong_count: number
    no_answer_count: number

    eliminated_count: number
    remaining_count: number

    opened_at: string | null
    closed_at: string | null
}
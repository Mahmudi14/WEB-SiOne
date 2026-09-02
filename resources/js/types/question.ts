export type QuestionOptionLabel = 'A' | 'B' | 'C' | 'D'

export interface QuestionOption {
    id: number
    label: QuestionOptionLabel
    text: string
    is_correct: boolean
}

export interface Question {
    id: number
    text: string
    position: number
    options: QuestionOption[]
}

export interface QuestionOptionForm {
    text: string
    is_correct: boolean
}

export interface QuestionFormData {
    text: string
    options: QuestionOptionForm[]
}
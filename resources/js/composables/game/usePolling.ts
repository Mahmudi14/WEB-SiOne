import {
    onBeforeUnmount,
    onMounted,
} from 'vue'

interface PollingOptions {
    interval?: number
    immediate?: boolean
}

interface ErrorResponse {
    message?: string
}

export function usePolling<T>(
    url: string,
    onData: (
        data: T,
    ) => void,
    options: PollingOptions = {},
) {
    const interval =
        options.interval ??
        2000

    const immediate =
        options.immediate ??
        true

    let timer:
        | ReturnType<
            typeof setInterval
        >
        | null = null

    let controller:
        | AbortController
        | null = null

    let processing =
        false

    let lastError =
        ''

    const dispatchError = (
        errorMessage: string,
    ) => {
        if (
            lastError ===
            errorMessage
        ) {
            return
        }

        lastError =
            errorMessage

        window.dispatchEvent(
            new CustomEvent(
                'sione:error',
                {
                    detail: {
                        title:
                            'Terjadi Kesalahan',

                        message:
                            errorMessage,
                    },
                },
            ),
        )
    }

    const poll = async () => {
        if (
            processing ||
            document.hidden
        ) {
            return
        }

        processing =
            true

        controller =
            new AbortController()

        try {
            const response =
                await fetch(
                    url,
                    {
                        method:
                            'GET',

                        credentials:
                            'same-origin',

                        signal:
                            controller
                                .signal,

                        headers: {
                            Accept:
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest',
                        },
                    },
                )

            if (!response.ok) {
                let errorMessage =
                    `Gagal memperbarui status permainan (${response.status}).`

                try {
                    const errorData =
                        await response
                            .json() as ErrorResponse

                    if (
                        errorData
                            .message
                    ) {
                        errorMessage =
                            errorData
                                .message
                    }
                } catch {
                    //
                }

                dispatchError(
                    errorMessage,
                )

                return
            }

            const data =
                await response
                    .json() as T

            lastError =
                ''

            onData(
                data,
            )
        } catch (error) {
            if (
                error instanceof
                    DOMException &&
                error.name ===
                    'AbortError'
            ) {
                return
            }

            dispatchError(
                'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
            )
        } finally {
            processing =
                false
        }
    }

    const start = () => {
        if (immediate) {
            void poll()
        }

        timer =
            setInterval(
                () => {
                    void poll()
                },
                interval,
            )
    }

    onMounted(
        start,
    )

    onBeforeUnmount(
        () => {
            if (timer) {
                clearInterval(
                    timer,
                )
            }

            controller
                ?.abort()
        },
    )

    return {
        poll,
    }
}
export type Exception<M = unknown> = Readonly<{
  name: string
  message: string
  code: string
  meta?: M
}>

type Props<M> = Omit<Exception<M>, 'name'> & { name?: string }

export class BaseError<M> extends Error implements Exception<M> {
  public readonly code: string
  public readonly meta?: M

  constructor(props: Props<M>) {
    super()
    this.name = props.name || props.code
    this.code = props.code
    this.meta = props.meta
    this.message = props.message

    Error.captureStackTrace(this, BaseError)
  }
}

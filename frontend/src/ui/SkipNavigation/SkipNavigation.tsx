'use client'

import { styled } from '@mui/joy/styles'

export const SkipNavigation = styled('a')`
  left: -10000px;
  position: absolute;
  z-index: 1;

  &:focus {
    left: 0;
    right: 0;
    top: 5px;
    margin: 0 auto;
    width: fit-content;
  }
`

export const Main = styled('main')`
  width: 100%;
  height: 100%;
  padding: 30px 0 0;

  &:focus {
    outline: none;
  }
`

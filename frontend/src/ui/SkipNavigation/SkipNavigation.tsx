'use client'

import { styled } from '@mui/joy/styles'

export const SkipNavigation = styled('a')`
  left: -10000px;
  position: absolute;
  z-index: 1;

  &:focus {
    right: 5px;
    top: 5px;
    left: unset;
  }
`

export const Main = styled('main')`
  width: 100%;
  height: 100%;
  padding: 30px 0;

  &:focus {
    outline: none;
  }
`

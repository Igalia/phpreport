import '@testing-library/jest-dom'
import { Session } from 'next-auth'

Element.prototype.scrollIntoView = () => {}

Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: jest.fn().mockImplementation((query) => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: jest.fn(), // deprecated
    removeListener: jest.fn(), // deprecated
    addEventListener: jest.fn(),
    removeEventListener: jest.fn(),
    dispatchEvent: jest.fn()
  }))
})

// **Global Mocks**

// Due to Jest transformer issues, we mock next-auth's  getServerSession and the authOptions directly:
export const mockSession: Session = {
  expires: new Date(Date.now() + 2 * 86400).toISOString(),
  user: {
    name: 'user',
    id: 0,
    username: 'username',
    email: 'user@igalia.com',
    firstName: 'user',
    lastName: 'last name',
    capacities: [],
    roles: ['staff', 'admin'],
    authorizedScopes: ['client:read']
  }
}

jest.mock('./src/app/api/auth/[...nextauth]/route', () => {
  return { authOptions: () => {} }
})

jest.mock('next-auth', () => {
  return {
    __esModule: true,
    getServerSession: jest.fn(() => Promise.resolve(mockSession))
  }
})

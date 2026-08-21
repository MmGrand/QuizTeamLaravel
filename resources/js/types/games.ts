export type VenueOption = {
    id: number;
    name: string;
    address?: string | null;
};

export type OrganizerOption = {
    id: number;
    name: string;
};

export type MemberOption = {
    id: number;
    name: string;
};

export type GameParticipant = {
    id: number;
    name: string;
};

export type GameListItem = {
    id: number;
    title: string | null;
    playedAt: string;
    score: number;
    place: number | null;
    notes: string | null;
    venue: { name: string };
    organizer: { name: string };
    participants: GameParticipant[];
};

export type GameFormValues = {
    id: number;
    venueId: number;
    organizerId: number;
    title: string | null;
    playedAt: string;
    score: number;
    place: number | null;
    notes: string | null;
    participantIds: number[];
};

export type GamePermissions = {
    canDeleteGame: boolean;
};

export type GameSummary = {
    id: number;
    title: string | null;
    playedAt: string;
    score: number;
    place: number | null;
    venue: { name: string };
    organizer: { name: string };
};

export type TeamStats = {
    gamesCount: number;
    totalScore: number;
    averageScore: number | null;
    bestScore: number | null;
    wins: number;
};
